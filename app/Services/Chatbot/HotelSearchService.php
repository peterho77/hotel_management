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
        $searchType = $this->detectSearchType($message); // Kết quả: 'product', 'service' hoặc 'all'

        // 2. Lọc bỏ các từ khóa quá chung chung (thuốc, sản phẩm, danh sách...)
        // BƯỚC QUAN TRỌNG: Loại bỏ từ 'danh sách', 'tất cả' để biến câu lệnh thành tìm kiếm rỗng (List All)
        $keywords = $this->filterGenericKeywords($keywords);

        $result = [
            'rooms'     => collect([]),
            'products'  => collect([]),
            'services'  => collect([]),
        ];

        // --- TRUY VẤN DATABASE ---
        // Nếu keywords rỗng nhưng $searchType xác định được (vd: 'product'), smartSearch sẽ trả về top 5
        
        if ($searchType === 'product' || $searchType === 'all') {
            // Tìm phòng
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
            'bao', 'nhiêu', 'tiền', 'có', 'không', 'là', 'của', 'và', 'cho', 
            'tôi', 'được', 'thì', 'như', 'nào', 'gì', 'về', 'này', 'đó', 
            'vậy', 'à', 'ạ', 'nhé', 'nha', 'từ', 'đến', 'tới', 
            'tìm', 'muốn', 'cần', 'biết', 'hỏi', 'xem', 
            'mình', 'bạn', 'em', 'anh', 'chị', 'cô', 'chú', 'bác',

            'thông', 'tin', 'chi', 'tiết', 'cụ', 'thể', 'tư', 'vấn', 'hỗ', 'trợ',

            'hi', 'hello', 'xin', 'chào', 'hotel', 'ad', 'admin', 'ơi', 'alo', 'hế', 'lô', 'giúp'
        ];

        // Loại bỏ ký tự đặc biệt
        $message = str_replace([',', '.', '?', '!', ';', ':', '-', '(', ')', '"', "'"], ' ', $message);

        $words = preg_split('/\s+/', $message);

        $keywords = array_filter($words, function ($word) use ($stopwords) {
            return !in_array($word, $stopwords) && mb_strlen($word, 'UTF-8') >= 2;
        });

        return array_values($keywords);
    }

    // trích xuất khoảng giá
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

    // tìm kiếm thông minh
    private function smartSearch(array $keywords, ?array $priceRange, string $modelClass, string $nameColumn): Collection
    {
        $query = $modelClass::query();

        // 1. Điều kiện Active
        if ($modelClass === RoomType::class) {
            $query->where('status', 'active');
        } elseif ($modelClass === Service::class || $modelClass === Product::class) {
            $query->where('is_active', true);
        }

        // 2. Lọc theo giá nếu có
        if ($priceRange) {
            $colPrice = ($modelClass === Service::class || $modelClass === Product::class) ? 'selling_price' : 'base_price';
            $query->whereBetween($colPrice, [$priceRange['min'], $priceRange['max']]);
        }

        // --- LOGIC MỚI: TÌM TẤT CẢ (FALLBACK) ---
        // Nếu không có keywords (do đã bị lọc hết bởi filterGenericKeywords)
        // Thì trả về danh sách mặc định (Limit 5)
        if (empty($keywords)) {
            if ($modelClass === RoomType::class) {
                 $query->with(['rooms', 'amenities']); 
            }
            // Sắp xếp mới nhất hoặc theo tên để danh sách đẹp hơn
            return $query->latest()->limit(5)->get();
        }
        // ----------------------------------------

        // 3. Nếu có keywords: Tìm chính xác
        $exactQuery = clone $query;
        foreach ($keywords as $keyword) {
            $exactQuery->where(function ($q) use ($keyword, $nameColumn, $modelClass) {
                $q->where($nameColumn, 'like', '%' . $keyword . '%');
                if ($modelClass === RoomType::class) {
                    $q->orWhere('bed_type', 'like', '%' . $keyword . '%')
                        ->orWhere('description', 'like', '%' . $keyword . '%');
                }
            });
        }
        $results = $exactQuery->limit(5)->get();

        // 4. Tìm gần đúng (Fuzzy) nếu tìm chính xác thất bại
        if ($results->isEmpty() && count($keywords) > 1) {
            $fuzzyQuery = clone $query;
            $fuzzyQuery->where(function ($q) use ($keywords, $nameColumn, $modelClass) {
                foreach ($keywords as $keyword) {
                    $q->orWhere($nameColumn, 'like', '%' . $keyword . '%');
                    if ($modelClass === RoomType::class) {
                        $q->orWhere('bed_type', 'like', '%' . $keyword . '%');
                    }
                }
            });
            $results = $fuzzyQuery->limit(5)->get();
        }

        // Load relations
        if ($results->isNotEmpty()) {
            if ($modelClass === RoomType::class){
                $results->load(['rooms', 'amenities', 'room_options', 'branches']);
            }
        }

        return $results;
    }

    private function normalizePrice(string $price): ?int
    {
        $price = preg_replace('/[^\d.,]/', '', $price);
        $price = str_replace([',', '.'], '', $price);
        if (preg_match('/(\d+)\s*(?:k|ngàn)/i', $price, $m)) {
            return (int) $m[1] * 1000;
        }
        return $price ? (int) $price : null;
    }

    public function extractProductImages(array $searchResults): array
    {
        $images = [];
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

    private function detectSearchType(string $message): string
    {
        $message = mb_strtolower($message, 'UTF-8');
        $productKeywords = ['phòng', 'loại phòng', 'hạng phòng', 'hàng hóa', 'thức ăn', 'đồ uống', 'mì gói', 'bia', 'nước ngọt', 'chai', 'hộp'];
        $serviceKeywords = ['dịch vụ', 'thuê', 'giặt', 'golf', 'xe', 'massage', 'spa'];

        $hasProduct = false;
        $hasService = false;

        foreach ($productKeywords as $keyword) {
            if (mb_strpos($message, $keyword) !== false) {
                $hasProduct = true;
                break;
            }
        }
        foreach ($serviceKeywords as $keyword) {
            if (mb_strpos($message, $keyword) !== false) {
                $hasService = true;
                break;
            }
        }

        if ($hasProduct && !$hasService) return 'product';
        if ($hasService && !$hasProduct) return 'service';
        return 'all';
    }

    // --- CẬP NHẬT QUAN TRỌNG NHẤT Ở ĐÂY ---
    // lọc bỏ các từ khóa quá chung chung VÀ CÁC TỪ KHÓA MANG Ý NGHĨA "DANH SÁCH/TẤT CẢ"
    private function filterGenericKeywords(array $keywords): array
    {
        $genericWords = [
            // Từ khóa hành động/chung chung cũ
            'đặt', 'tìm', 'cần', 'muốn', 'giá', 
            'chi', 'tiết', 'bán', 'mua',
            
            // --- TỪ KHÓA MỚI THÊM VÀO ĐỂ XỬ LÝ "DANH SÁCH/TẤT CẢ" ---
            'danh', 'sách',      // cho cụm "danh sách"
            'tất', 'cả',         // cho cụm "tất cả"
            'toàn', 'bộ',        // cho cụm "toàn bộ"
            'list', 'menu',      // tiếng Anh
            'các', 'những',      // số nhiều
            'loại', 'kiểu',

            // --- BỔ SUNG QUAN TRỌNG: CÁC DANH TỪ CHUNG CỦA ĐỐI TƯỢNG ---
            // Phải xóa các từ này đi thì code mới hiểu là "Tìm tất cả dịch vụ" thay vì "Tìm dịch vụ tên là Dịch Vụ"
            'phòng', 'loại', 'hạng', 'kiểu', // room keywords
            'hàng', 'hóa', 'sản', 'phẩm',    // product keywords
            'dịch', 'vụ', 'service',         // service keywords <--- THÊM DÒNG NÀY
            'đồ', 'ăn', 'uống'               // food keywords
        ];

        return array_filter($keywords, function ($keyword) use ($genericWords) {
            return !in_array($keyword, $genericWords);
        });
    }

    public function formatForGemini(array $searchResults): string
    {
        $formatted = "THÔNG TIN PHÒNG/DỊCH VỤ LIÊN QUAN:\n\n";

        if ($this->isAskingPrice) {
            $formatted .= "!!! LƯU Ý: KHÁCH HỎI GIÁ -> ĐƯA GIÁ LÊN ĐẦU.\n\n";
        }
        
        if ($searchResults['rooms']->isEmpty() && $searchResults['products']->isEmpty() && $searchResults['services']->isEmpty()) {
            return "Không tìm thấy thông tin cụ thể nào trong hệ thống.";
        }

        if ($searchResults['rooms']->isNotEmpty()) {
            foreach ($searchResults['rooms'] as $room) {
                $formatted .= "- Phòng: {$room->name}\n";
                $formatted .= "  Giá: " . number_format($room->base_price) . " VNĐ | Trạng thái: " . ($room->total_quantity > 0 ? "Còn" : "Hết") . "\n";
                $shortDesc = Str::limit($room->bed_type, 100);
                $formatted .= "  Giường: {$shortDesc}\n---\n";
            }
        }

        if ($searchResults['products']->isNotEmpty()) {
            foreach ($searchResults['products'] as $product) {
                $formatted .= "- Sản phẩm: {$product->name}\n";
                $formatted .= "  Giá: " . number_format($product->selling_price) . " VNĐ\n---\n";
            }
        }

        if ($searchResults['services']->isNotEmpty()) {
            foreach ($searchResults['services'] as $service) {
                $formatted .= "- Dịch vụ: {$service->name}\n";
                $formatted .= "  Giá: " . number_format($service->selling_price) . " VNĐ\n---\n";
            }
        }

        return $formatted;
    }
}