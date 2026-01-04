<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Chatbot\HotelSearchService;
use Illuminate\Support\Facades\Http;
use Exception;

class ChatbotController extends Controller
{
    protected $hotelSearch;

    public function __construct()
    {
        $this->hotelSearch = new HotelSearchService();
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = $request->input('message');

        return response()->stream(
            function () use ($userMessage) {
                try {
                    // 1. Tìm kiếm dữ liệu Hotel (Search Service)
                    $searchResults = $this->hotelSearch->search($userMessage);

                    // --- Gửi ảnh trước ---
                    $productImages = $this->hotelSearch->extractProductImages($searchResults);
                    if (!empty($productImages)) {
                        echo "event: images\n";
                        echo "data: " . json_encode($productImages) . "\n\n";
                        if (ob_get_level() > 0) ob_flush();
                        flush();
                    }

                    // 2. Chuẩn bị Prompt cho Gemini
                    $productInfo = $this->hotelSearch->formatForGemini($searchResults);
                    $apiKey = config('services.gemini.api_key');

                    if (empty($apiKey)) throw new Exception('API key chưa cấu hình');

                    $prompt = $this->buildHotelPrompt($userMessage, $productInfo);

                    // 3. Gọi API Gemini (Chế độ Non-Stream)
                    $response = Http::timeout(30)->post(
                        "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey,
                        [
                            'contents' => [['parts' => [['text' => $prompt]]]]
                        ]
                    );

                    if ($response->successful()) {
                        $responseData = $response->json();
                        $content = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';
                        
                        // --- Xử lý tách từ để tạo hiệu ứng gõ ---
                        // Mẹo: Dùng preg_split để giữ cả dấu xuống dòng (\n)
                        $words = preg_split('/( )/', $content, -1, PREG_SPLIT_DELIM_CAPTURE);

                        foreach ($words as $index => $word) {
                            echo "event: update\n";
                            echo "data: " . json_encode($word) . "\n\n"; // Encode JSON để an toàn với ký tự đặc biệt

                            // Flush buffer
                            if (ob_get_level() > 0) ob_flush();
                            flush();
                            
                            // Nghỉ 30ms mỗi từ (nhanh hơn chút cho tự nhiên)
                            usleep(30000); 
                        }
                    } else {
                        $this->sendError("Xin lỗi, hệ thống đang bận.");
                    }

                } catch (Exception $e) {
                    $this->sendError("Có lỗi xảy ra: " . $e->getMessage());
                }
            },
            200,
            [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no',
            ]
        );
    }

    // Prompt chuẩn cho Khách Sạn
    private function buildHotelPrompt(string $userMessage, string $productInfo): string
    {
        return <<<PROMPT
        Bạn là nhân viên lễ tân chuyên nghiệp tại khách sạn Sona Hotel.
        
        NHIỆM VỤ:
        Trả lời câu hỏi của khách hàng dựa trên dữ liệu phòng/dịch vụ bên dưới.

        DỮ LIỆU HIỆN CÓ:
        {$productInfo}

        YÊU CẦU:
        1. Giọng văn lịch sự, thân thiện, xưng "em" gọi khách là "anh/chị".
        2. Nếu khách hỏi giá: Báo giá chính xác kèm đơn vị tiền tệ.
        3. Nếu không có thông tin trong dữ liệu: Xin lỗi và đề xuất khách liên hệ hotline.
        4. KHÔNG bịa đặt thông tin không có trong dữ liệu.

        CÂU HỎI: "{$userMessage}"
        TRẢ LỜI:
        PROMPT;
    }

    // Hàm phụ trợ để gửi lỗi nhanh
    private function sendError($message) {
        echo "event: update\n";
        echo "data: " . json_encode($message) . "\n\n";
        if (ob_get_level() > 0) ob_flush();
        flush();
    }
}