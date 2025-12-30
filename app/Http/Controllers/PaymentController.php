<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Payment\VNPayService;
use App\Services\EmailSMTP\EmailService;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\RoomOption;

class PaymentController extends Controller
{
    protected VNPayService $vnpayService;
    protected EmailService $emailService;

    public function __construct(VNPayService $vnpayService, EmailService $emailService)
    {
        $this->vnpayService = $vnpayService;
        $this->emailService = $emailService;
    }

    // init VNPAY payment 
    public function vnpayCheckout(int $booking_id)
    {
        $booking = Booking::findOrFail($booking_id);
        $url = $this->vnpayService->generatePaymentUrl($booking);

        return redirect()->away($url);
    }

    // return URL from VNPAY
    public function vnpayReturn(Request $request)
    {
        $result = $this->vnpayService->processReturn($request->all());

        if ($result['success']) {
            try {
                $booking = Booking::with('room_booking_items')->find($result['bookingId']);

                if ($booking) {
                    // Chỉ xử lý nếu đơn đang pending
                    if ($booking->status === 'pending') {

                        DB::transaction(function () use ($booking) {
                            $booking->update(['status' => 'confirmed']);

                            $booking->payments()->where('status', 'pending')->update(['status' => 'paid']);

                            // Duyệt qua từng item (mỗi item là 1 phòng được đặt)
                            foreach ($booking->room_booking_items as $item) {
                                // Lấy thông tin RoomOption của item này để biết nó thuộc RoomType nào
                                $currentOption = RoomOption::find($item->room_option_id);

                                if ($currentOption) {
                                    // Logic: Tìm TẤT CẢ các option thuộc cùng loại phòng (room_type_id)
                                    // và trừ available_quantity đi 1
                                    RoomOption::where('room_type_id', $currentOption->room_type_id)
                                        ->where('available_quantity', '>', 0) // Check > 0 để tránh âm
                                        ->decrement('available_quantity', 1);
                                }
                            }
                        });

                        // Đặt trong try-catch riêng để nếu lỗi mail cũng KHÔNG làm lỗi booking
                        try {
                            Log::info("Bắt đầu gửi email xác nhận cho Booking ID: " . $booking->id);
                            $this->emailService->sendBookingConfirmation($booking);
                            Log::info("Đã gửi email thành công.");
                        } catch (\Exception $e) {
                            // Ghi log lỗi để debug nguyên nhân không gửi được mail
                            Log::error("Lỗi gửi email confirmation: " . $e->getMessage());
                        }
                    }
                }

                return redirect()->route('booking.index')->with('success', $result['message']);
            } catch (\Exception $e) {
                Log::error('Lỗi xử lý sau khi thanh toán VNPay', [
                    'booking_id' => $result['bookingId'] ?? 'N/A',
                    'error' => $e->getMessage()
                ]);

                return redirect()->route('booking.index')->with('error', 'Thanh toán thành công nhưng xảy ra lỗi hệ thống. Vui lòng liên hệ CSKH.');
            }
        }

        // Trường hợp thanh toán thất bại
        return redirect()->route('booking.index')->with('error', $result['message']);
    }

    public function vnpayIpn(Request $request)
    {
        $result = $this->vnpayService->processReturn($request->all());
        return response()->json([
            'RspCode' => $result['success'] ? '00' : '97',
            'Message' => $result['message'] ?? 'Invalid signature',
        ]);
    }
}
