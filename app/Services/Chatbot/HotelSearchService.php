<?php

namespace App\Services\Chatbot;

use App\Models\RoomType;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class HotelSearchService
{
    protected $isAskingPrice = false;

    public function search(string $message): array
    {
        $this->detectPriceIntent($message);
        // 1. Trích xuất từ khóa và giá
        $keywords   = $this->extractKeywords($message);
        $priceRange = $this->extractPriceRange($message);
        $searchType = $this->detectSearchType($message);

        // 2. Lọc bỏ các từ khóa quá chung chung (thuốc, sản phẩm...)
        $keywords = $this->filterGenericKeywords($keywords);

        // Nếu sau khi lọc không còn từ khóa và không có khoảng giá -> trả về kết quả rỗng, không truy vấn DB
        if (empty($keywords) && empty($priceRange)) {
            return [
                'rooms' => collect([]),
                'products'     => collect([]),
                'services'  => collect([]),
            ];
        }

        $result = [
            'rooms' => collect([]),
            'products'     => collect([]),
            'services'  => collect([]),
        ];

        // Truy vấn Database khi có từ khóa hoặc khoảng giá
        if ($searchType === 'product' || $searchType === 'all') {
            // Tìm thuốc
            $result['rooms'] = $this->smartSearch($keywords, $priceRange, RoomType::class, 'name');
            // Tìm hàng hóa
            $result['products'] = $this->smartSearch($keywords, $priceRange, Product::class, 'name');
        }

        if ($searchType === 'service' || $searchType === 'all') {
            // Tìm dịch vụ
            $result['services'] = $this->smartSearch($keywords, $priceRange, Service::class, 'name');
        }

        return $result;
    }

    // xử lý từ khóa từ user nhập vào
    private function extractKeywords(string $message): array
    {
        $message = mb_strtolower($message, 'UTF-8');

        // Danh sách từ dừng (Stopwords)
        $stopwords = [
            // Nhóm câu hỏi & đại từ
            'bao',
            'nhiêu',
            'tiền',
            'có',
            'không',
            'là',
            'của',
            'và',
            'cho',
            'tôi',
            'mua',
            'được',
            'thì',
            'như',
            'nào',
            'gì',
            'về',
            'này',
            'đó',
            'vậy',
            'à',
            'ạ',
            'nhé',
            'nha',
            'từ',
            'đến',
            'tới',
            'tìm',
            'sản',
            'phẩm',
            'muốn',
            'cần',
            'biết',
            'hỏi',
            'xem',
            'mình',
            'bạn',
            'em',
            'anh',
            'chị',
            'cô',
            'chú',
            'bác',

            //Nhóm từ "thông tin" gây lỗi tìm kiếm
            'thông',
            'tin',
            'chi',
            'tiết',
            'cụ',
            'thể',
            'tư',
            'vấn',
            'hỗ',
            'trợ',

            // Từ khóa chào hỏi
            'hi',
            'hello',
            'xin',
            'chào',
            'hotel',
            'ad',
            'admin',
            'ơi',
            'alo',
            'hế',
            'lô',
            'giúp'
        ];

        // Loại bỏ ký tự đặc biệt
        $message = str_replace([',', '.', '?', '!', ';', ':', '-', '(', ')', '"', "'"], ' ', $message);

        $words = preg_split('/\s+/', $message);

        $keywords = array_filter($words, function ($word) use ($stopwords) {
            // Giữ lại từ >= 2 ký tự và không nằm trong stopwords
            return !in_array($word, $stopwords) && mb_strlen($word, 'UTF-8') >= 2;
        });

        return array_values($keywords);
    }


    // trích xuất khoảng giá từ tin nhắn
    private function extractPriceRange(string $message): ?array
    {
        $message  = mb_strtolower($message, 'UTF-8');
        $patterns = [
            '/từ\s*(\d+(?:[.,]\d+)?)\s*(?:ngàn|k|000)?\s*(?:vnđ|đ|vnd)?\s*(?:đến|tới|-)\s*(\d+(?:[.,]\d+)?)\s*(?:ngàn|k|000)?\s*(?:vnđ|đ|vnd)?/i',
            '/(\d+(?:[.,]\d+)?)\s*(?:ngàn|k|000)?\s*(?:vnđ|đ|vnd)?\s*(?:đến|tới|-)\s*(\d+(?:[.,]\d+)?)\s*(?:ngàn|k|000)?\s*(?:vnđ|đ|vnd)?/i',
            '/khoảng\s*(\d+(?:[.,]\d+)?)\s*(?:ngàn|k|000)?\s*(?:vnđ|đ|vnd)?\s*(?:đến|tới|-)\s*(\d+(?:[.,]\d+)?)\s*(?:ngàn|k|000)?\s*(?:vnđ|đ|vnd)?/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                $min = $this->normalizePrice($matches[1]);
                $max = $this->normalizePrice($matches[2]);
                if ($min && $max && $min <= $max) {
                    return ['min' => $min, 'max' => $max];
                }
            }
        }
        return null;
    }

    // phát hiện ý định hỏi về giá
    private function detectPriceIntent(string $message): void
    {
        $priceKeywords = ['giá', 'tiền', 'bao nhiêu', 'nhiêu', 'chi phí', 'cost', 'price', 'how much', 'money'];
        $message = mb_strtolower($message);
        foreach ($priceKeywords as $word) {
            if (str_contains($message, $word)) {
                $this->isAskingPrice = true;
                return;
            }
        }
        $this->isAskingPrice = false;
    }

    // tìm kiếm thông minh với 2 chiến lược
    private function smartSearch(array $keywords, ?array $priceRange, string $modelClass, string $nameColumn): Collection
    {
        $query = $modelClass::query();

        // chiến lược 1 - Lọc loại phòng, hàng hóa, dịch vụ đang kinh doanh
        if ($modelClass === RoomType::class) {
            $query->where('status', 'active');
        } elseif ($modelClass === Service::class || $modelClass === Product::class) {
            $query->where('is_active', true);
        }

        // Sản phẩm phải chứa TẤT CẢ từ khóa
        $exactQuery = clone $query;
        foreach ($keywords as $keyword) {
            $exactQuery->where(function ($q) use ($keyword, $nameColumn, $modelClass) {
                $q->where($nameColumn, 'like', '%' . $keyword . '%');
                // Thêm tìm kiếm trường phụ 
                if ($modelClass === RoomType::class) {
                    $q->orWhere('bed_type', 'like', '%' . $keyword . '%')
                        ->orWhere('description', 'like', '%' . $keyword . '%');
                }
            });
        }

        if ($priceRange) {
            $colPrice = ($modelClass === Service::class || $modelClass === Product::class) ? 'selling_price' : 'base_price';
            $exactQuery->whereBetween($colPrice, [$priceRange['min'], $priceRange['max']]);
        }

        $results = $exactQuery->limit(5)->get();

        // chiến lược 2 - Nếu không tìm thấy kết quả, tìm loại phòng/hàng hóa/dịch vụ chứa "bất kỳ" từ khóa nào
        // Nếu tìm chính xác không ra VÀ có nhiều từ khóa -> Chuyển sang tìm loại phòng/hàng hóa/dịch vụ chứa "bất kỳ" từ nào
        if ($results->isEmpty() && count($keywords) > 1) {
            $fuzzyQuery = clone $query;
            $fuzzyQuery->where(function ($q) use ($keywords, $nameColumn, $modelClass) {
                foreach ($keywords as $keyword) {
                    $q->orWhere($nameColumn, 'like', '%' . $keyword . '%');
                    if ($modelClass === RoomType::class) {
                        $q->orWhere('bed_type', 'like', '%' . $keyword . '%')
                            ->orWhere('description', 'like', '%' . $keyword . '%');
                    }
                }
            });

            if ($priceRange) {
                $colPrice = ($modelClass === Service::class || $modelClass === Product::class) ? 'selling_price' : 'base_price';
                $fuzzyQuery->whereBetween($colPrice, [$priceRange['min'], $priceRange['max']]);
            }

            $results = $fuzzyQuery->limit(5)->get();
        }

        // Load quan hệ nếu có kết quả
        if ($results->isNotEmpty()) {
            if ($modelClass === RoomType::class){
                $results->load(['rooms', 'amenities', 'room_options', 'branches']);
            }
            // elseif ($modelClass === Product::class) {
            //     $results->load(['category', 'doctor']);
            // } 
            // elseif ($modelClass === Service::class) {
            //     $results->load(['category', 'doctor']);
            // }
        }

        return $results;
    }

    // chuẩn hóa giá tiền
    private function normalizePrice(string $price): ?int
    {
        $price = preg_replace('/[^\d.,]/', '', $price);
        $price = str_replace([',', '.'], '', $price);
        if (preg_match('/(\d+)\s*(?:k|ngàn)/i', $price, $m)) {
            return (int) $m[1] * 1000;
        }
        return $price ? (int) $price : null;
    }

    // trích xuất ảnh sản phẩm
    public function extractProductImages(array $searchResults): array
    {
        $images = [];

        // 1. Lấy ảnh thuốc
        foreach ($searchResults['rooms'] as $room) {
            if (!empty($room->image)) {
                $images[] = [
                    'id'    => $room->id,
                    'name'  => $room->name,
                    'price' => $room->base_price ?? number_format($room->gia_ban) . ' đ',
                    'image' => asset('storage/' . $room->image),
                    'type'  => 'room_type'
                ];
            }
        }

        // 2. Lấy ảnh vật tư y tế
        foreach ($searchResults['products'] as $product) {
            if (!empty($product->image)) {
                $images[] = [
                    'id'    => $product->id,
                    'name'  => $product->name,
                    'price' => $product->selling_price ?? number_format($product->selling_price) . ' đ',
                    'image' => asset('storage/' . $product->image),
                    'type'  => 'product'
                ];
            }
        }
        return $images;
    }

    // Phát hiện loại tìm kiếm mà khách hàng muốn: "thuốc/sản phẩm", "dịch vụ" hay "cả hai"
    private function detectSearchType(string $message): string
    {
        $message = mb_strtolower($message, 'UTF-8');

        $productKeywords = [
            'phòng',
            'loại phòng',
            'hạng phòng',
            'hàng hóa',
            'thức ăn',
            'đồ uống',
            'mì gói',
            'bia',
            'nước ngọt',
            'chai',
            'hộp',
        ];
        $serviceKeywords = [
            'dịch vụ',
            'thuê',
            'giặt',
            'golf',
            'xe',
            'massage'
        ];

        $hasProduct = false;
        $hasService = false;

        // Kiểm tra từ khóa liên quan đến sản phẩm/thuốc
        foreach ($productKeywords as $keyword) {
            if (mb_strpos($message, $keyword) !== false) {
                $hasProduct = true;
                break;
            }
        }
        // Kiểm tra từ khóa liên quan đến dịch vụ
        foreach ($serviceKeywords as $keyword) {
            if (mb_strpos($message, $keyword) !== false) {
                $hasService = true;
                break;
            }
        }

        // Ưu tiên phân loại rõ ràng, nếu có cả hai thì trả về 'all'
        if ($hasProduct && !$hasService) {
            return 'product';
        }
        if ($hasService && !$hasProduct) {
            return 'service';
        }
        return 'all';
    }

    // lọc bỏ các từ khóa quá chung chung (thuốc, sản phẩm...)
    private function filterGenericKeywords(array $keywords): array
    {
        $genericWords = [
            'đặt',
            'phòng',
            'hàng',
            'hóa',
            'tìm',
            'cần',
            'muốn',
            'giá',
            'chi',
            'tiết',
            'bán',
            'mua'
        ];

        return array_filter($keywords, function ($keyword) use ($genericWords) {
            return !in_array($keyword, $genericWords);
        });
    }



    public function formatForGemini(array $searchResults): string
    {
        $formatted = "THÔNG TIN PHÒNG/DỊCH VỤ LIÊN QUAN:\n\n";

        if ($this->isAskingPrice) {
            $formatted .= "!!! LƯU Ý QUAN TRỌNG: KHÁCH HÀNG ĐANG HỎI VỀ GIÁ TIỀN. Hãy trả lời ngắn gọn, đưa GIÁ TIỀN lên đầu câu trả lời.\n\n";
        }
        // Kiểm tra nếu tất cả đều rỗng -> Trả về thông báo không tìm thấy
        if (
            $searchResults['rooms']->isEmpty() &&
            $searchResults['products']->isEmpty() &&
            $searchResults['services']->isEmpty()
        ) {
            return "Không tìm thấy thông tin cụ thể nào trong hệ thống. Hãy thử tìm kiếm từ khóa khác.";
        }

        if ($searchResults['rooms']->isNotEmpty()) {
            foreach ($searchResults['rooms'] as $index => $room) {
                $formatted .= "- Phòng: {$room->name}\n";
                $formatted .= "  Giá: {$room->base_price} | Tình trạng: " . ($room->total_quantity > 0 ? "Còn phòng" : "Hết") . "\n";
                $shortDesc = Str::limit($room->bed_type ?? 'Không có', 150);
                $formatted .= "  Loại giường: {$shortDesc}\n";
                if ($room->mo_ta) {
                    $formatted .= "  [Thông tin chi tiết - Chỉ dùng khi khách hỏi sâu]: {$room->description}\n";
                }
                $formatted .= "---\n";
            }
        }

        if ($searchResults['products']->isNotEmpty()) {
            foreach ($searchResults['products'] as $index => $product) {
                $formatted .= "- Sản phẩm: {$product->name}\n";
                $formatted .= "  Giá: {$product->selling_price} | Kho: " . ($product->max_inventory > 0 ? "Còn hàng" : "Hết hàng") . "\n";
                if ($product->description) {
                    $formatted .= "  [Thông tin chi tiết - Chỉ dùng khi khách hỏi sâu]: {$product->description}\n";
                }
                $formatted .= "---\n";
            }
        }

        if ($searchResults['services']->isNotEmpty()) {
            foreach ($searchResults['services'] as $index => $service) {
                $formatted .= ($index + 1) . ". {$service->name}\n";
                $formatted .= "   - Giá: " . number_format($service->selling_price, 0, ',', '.') . " VNĐ\n";
                if ($service->duration) {
                    $formatted .= "   - Thời gian: {$service->duration}h\n";
                }
                if ($service->description) {
                    $formatted .= "   - Mô tả: {$service->description}\n";
                }
                $formatted .= "\n";
            }
        }

        return $formatted;
    }
}
