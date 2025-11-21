<?php

namespace App\Services\Payment;

use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class VNPayService
{
    public function generatePaymentUrl(Booking $booking): string
    {
        $vnpUrl = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
        $returnUrl = route('payment.vnpay.return', ['bookingId' => $booking->id]);
        $tmnCode = config('services.vnpay.tmn_code');
        $hashSecret = config('services.vnpay.hash_secret');

        $payment = $booking->payments()->latest()->first();

        $txnRef = $booking->id . '_' . time();
        $params = [
            'vnp_Version'    => '2.1.0',
            'vnp_TmnCode'    => $tmnCode,
            'vnp_Command'    => 'pay',
            'vnp_Amount'     => (int) ($booking->total_price * 100),
            'vnp_CurrCode'   => 'VND',
            'vnp_TxnRef'     => $txnRef,
            'vnp_OrderInfo'  => 'Booking order #' . $booking->id,
            'vnp_OrderType'  => 'other',
            'vnp_Locale'     => 'vn',
            'vnp_ReturnUrl'  => $returnUrl,
            'vnp_CreateDate' => now('Asia/Ho_Chi_Minh')->format('YmdHis'),
            'vnp_ExpireDate' => now('Asia/Ho_Chi_Minh')->addMinutes(15)->format('YmdHis'),
            'vnp_IpAddr'     => request()->ip(),
        ];

        // lọc rỗng và sort
        $filtered = array_filter($params, fn($v) => $v !== null && $v !== '');
        ksort($filtered);

        $query = '';
        $rawToSign = '';

        foreach ($filtered as $key => $value) {
            $encoded = urlencode($value);
            $query .= $key . "=" . $encoded . "&";
            $rawToSign .= $key . "=" . $encoded . "&";
        }

        $rawToSign = rtrim($rawToSign, '&');
        $secureHash = hash_hmac('sha512', $rawToSign, $hashSecret);

        $redirectUrl = $vnpUrl . '?' . $query . "vnp_SecureHash=" . $secureHash;

        Log::info('VNPAY: build payment url', [
            'booking_id' => $booking->id,
            'raw_to_sign' => $rawToSign,
            'secure_hash' => $secureHash,
            'url' => $redirectUrl,
        ]);

        // lưu txn ref
        $payment->transaction_id = $txnRef;
        $booking->status="confirmed";
        $payment->save();
        $booking->save();

        return $redirectUrl;
    }

    public function processReturn(array $data): array
    {
        $hashSecret = config('services.vnpay.hash_secret');
        $receivedHash = $data['vnp_SecureHash'] ?? '';
        unset($data['vnp_SecureHash'], $data['vnp_SecureHashType']);

        $filtered = [];
        foreach ($data as $k => $v) {
            if ($v !== null && $v !== '') {
                $filtered[$k] = $v;
            }
        }
        ksort($filtered);

        $pairs = [];
        foreach ($filtered as $k => $v) {
            $pairs[] = $k . '=' . urlencode($v);
        }
        $rawToSign = implode('&', $pairs);
        $calc = hash_hmac('sha512', $rawToSign, $hashSecret);

        if (!hash_equals($receivedHash, $calc)) {
            return ['success' => false, 'message' => 'Chữ ký không hợp lệ!'];
        }
        $bookingId = isset($filtered['vnp_TxnRef']) ? explode('_', $filtered['vnp_TxnRef'])[0] : null;
        if (!$bookingId) {
            return ['success' => false, 'message' => 'Thiếu mã đơn hàng'];
        }

        $booking = Booking::find($bookingId);
        $payment = $booking->payments()->latest()->first();
        if (!$booking) {
            return ['success' => false, 'message' => 'Không tìm thấy đơn hàng'];
        }

        $code = $filtered['vnp_ResponseCode'] ?? null;
        if ($code === '00') {
            $payment->status = 'paid';
            $payment->save();
            return [
                'success' => true,
                'message' => 'Thanh toán thành công!',
                'bookingId' => $booking->id,
            ];
        }

        return [
            'success' => false,
            'message' => 'Thanh toán thất bại! Mã: ' . ($code ?? 'N/A'),
            'bookingId' => $booking->id,
        ];
    }
}
