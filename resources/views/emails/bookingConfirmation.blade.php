<!DOCTYPE html>
<html lang="vi">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Xác nhận đặt phòng</title>
    </head>

    <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f5f5;">
        <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f5f5; padding: 20px;">
            <tr>
                <td align="center">
                    <table width="600" cellpadding="0" cellspacing="0"
                        style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <!-- Header -->
                        <tr>
                            <td style="background-color: #2563eb; padding: 30px; text-align: center;">
                                <h1 style="color: #ffffff; margin: 0; font-size: 24px;">Sona Hotel</h1>
                                <p style="color: #e0e7ff; margin: 10px 0 0 0;">Xác nhận đặt phòng</p>
                            </td>
                        </tr>

                        <!-- Content -->
                        <tr>
                            <td style="padding: 30px;">
                                <h1 style="color: #1f2937; font-size: 20px; margin: 0 0 20px 0;">Cảm ơn
                                    {{ $booking->customer->full_name }} đã đặt phòng!
                                </h1>
                                <p style="color: #4b5563; font-size: 14px; margin: 0 0 20px 0;">
                                    Mã đặt phòng: <strong
                                        style="color: #2563eb; font-size: 16px;">#{{ $booking->id }}</strong>
                                </p>

                                <div>
                                    <h3>Thông tin khách hàng</h3>
                                    <div style="display:flex; justify-content: space-between; align-items:center">
                                        <h4>Tên:</h4>
                                        <span>{{  $booking->customer->full_name }}</span>
                                    </div>
                                    @if ($booking->customer->phone)
                                        <div style="display:flex; justify-content: space-between; align-items:center">
                                            <h4>Số điện thoại:</h4>
                                            <span>{{  $booking->customer->phone }}</span>
                                        </div>
                                    @endif
                                    <div style="display:flex; justify-content: space-between; align-items:center">
                                        <h4>Số lượng khách:</h4>
                                        <span>
                                            {{  $booking->num_adults }} người lớn
                                            @if($booking->num_children > 0)
                                                và {{ $booking->num_children }} trẻ em
                                            @endif
                                        </span>
                                    </div>
                                    <div style="display:flex; justify-content: space-between; align-items:center">
                                        <h4>Yêu cầu đặc biệt:</h4>
                                        <span>{{  $booking->special_request }}</span>
                                    </div>
                                </div>

                                <h3 style="color: #1f2937; font-size: 16px; margin: 0 0 15px 0;">Chi tiết đặt phòng:
                                </h3>
                                <table width="100%" cellpadding="0" cellspacing="0"
                                    style="border-collapse: collapse; margin-bottom: 20px; border: 1px solid #ddd;">
                                    <thead>
                                        <tr style="background-color: #f3f4f6;">
                                            <th
                                                style="padding: 12px; text-align: left; border: 1px solid #ddd; color: #1f2937; font-size: 14px;">
                                                Số lượng phòng</th>
                                            <th
                                                style="padding: 12px; text-align: center; border: 1px solid #ddd; color: #1f2937; font-size: 14px;">
                                                Đêm</th>
                                            <th
                                                style="padding: 12px; text-align: right; border: 1px solid #ddd; color: #1f2937; font-size: 14px;">
                                                Check in</th>
                                            <th
                                                style="padding: 12px; text-align: right; border: 1px solid #ddd; color: #1f2937; font-size: 14px;">
                                                Check out</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td
                                                style="padding: 12px; border: 1px solid #ddd; color: #1f2937; font-size: 14px;">
                                                {{ $booking->num_rooms }}
                                            </td>
                                            <td
                                                style="padding: 12px; text-align: center; border: 1px solid #ddd; color: #4b5563; font-size: 14px;">
                                                {{ $booking->num_nights }}
                                            </td>
                                            <td
                                                style="padding: 12px; text-align: center; border: 1px solid #ddd; color: #4b5563; font-size: 14px;">
                                                {{ $booking->check_in }}
                                            </td>
                                            <td
                                                style="padding: 12px; text-align: center; border: 1px solid #ddd; color: #4b5563; font-size: 14px;">
                                                {{ $booking->check_out }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <p style="color: #1f2937; font-size: 16px; margin: 20px 0;">
                                    Tổng cộng: <span
                                        style="font-weight: bold; color: #dc2626; font-size: 18px;">{{ number_format($booking->total_price, 0, ',', '.') }}
                                        đ</span>
                                </p>
                            </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                            <td
                                style="background-color: #f9fafb; padding: 20px; text-align: center; border-top: 1px solid #e5e7eb;">
                                <p style="color: #6b7280; font-size: 12px; margin: 0 0 10px 0;">
                                    <strong>Sona Hotel</strong><br>
                                    Hotline: 1900 xxxx | Email: support@sonahotel.com
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>

</html>