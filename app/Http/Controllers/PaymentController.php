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
                // Eager load room_booking_items để tối ưu query
                $booking = Booking::with('room_booking_items')->find($result['bookingId']);

                if ($booking) {
                    // QUAN TRỌNG: Chỉ trừ số lượng nếu đơn hàng chưa được xử lý (tránh trừ đôi khi F5)
                    if ($booking->status === 'pending') {
                        DB::transaction(function () use ($booking) {
                            // 1. Cập nhật trạng thái Booking
                            $booking->update(['status' => 'confirmed']); // Hoặc 'paid' tùy enum của bạn

                            // 2. Cập nhật trạng thái thanh toán (Payment)
                            // Giả sử booking có quan hệ hasOne/hasMany với Payment
                            $booking->payments()->where('status', 'pending')->update(['status' => 'paid']);

                            // 3. Trừ số lượng available_quantity của RoomOption
                            foreach ($booking->room_booking_items as $item) {
                                // Lưu ý: Trong logic create trước đó, mỗi row item = 1 phòng
                                // Nên ta trừ 1 đơn vị cho mỗi item.
                                if ($item->room_option_id) {
                                    RoomOption::where('id', $item->room_option_id)
                                        ->where('available_quantity', '>', 0) // Kiểm tra để không bị số âm
                                        ->decrement('available_quantity');
                                }
                            }
                        });

                        // 4. Gửi email xác nhận (Chỉ gửi 1 lần khi xử lý thành công)
                        $this->emailService->sendBookingConfirmation($booking);
                    }
                }

                return redirect()->route('booking.index')->with('success', $result['message']);
            } catch (\Exception $e) {
                Log::error('Lỗi xử lý sau khi thanh toán VNPay', [
                    'booking_id' => $result['bookingId'],
                    'error' => $e->getMessage()
                ]);
                // Vẫn báo lỗi nhưng tiền đã trừ, cần xử lý thủ công hoặc log kỹ
                return redirect()->route('booking.index')->with('warning', 'Thanh toán thành công nhưng có lỗi khi cập nhật đơn hàng. Vui lòng liên hệ CSKH.');
            }
        }
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
