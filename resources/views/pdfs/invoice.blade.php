<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            body {
                font-family: 'DejaVu Sans', sans-serif;
            }

            /* Hỗ trợ tiếng Việt */
        </style>
    </head>

    <body class="p-10 text-gray-800">
        <div class="flex justify-between items-center border-b pb-4">
            <div>
                <h1 class="text-2xl font-bold">HÓA ĐƠN BÁN LẺ</h1>
                <p>Mã: {{ $order->code }}</p>
            </div>
            <div class="text-right">
                <p class="font-bold">KHÁCH SẠN CỦA BẠN</p>
                <p class="text-sm">Ngày: {{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <table class="w-full mt-8 border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2 text-left">#</th>
                    <th class="border p-2 text-left">Nội dung</th>
                    <th class="border p-2 text-center">SL</th>
                    <th class="border p-2 text-right">Đơn giá</th>
                    <th class="border p-2 text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                    <tr>
                        <td class="border p-2 text-center">{{ $index + 1 }}</td>
                        <td class="border p-2">
                            {{ $item->service_id ? $item->service->name : $item->product->name }}
                        </td>
                        <td class="border p-2 text-center">{{ $item->quantity }}</td>
                        <td class="border p-2 text-right">{{ number_format($item->price) }}</td>
                        <td class="border p-2 text-right">{{ number_format($item->subtotal) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-6 text-right space-y-2">
            <p>Tổng tiền: <span class="font-bold">{{ number_format($order->total_amount) }} VNĐ</span></p>
            <p>Đã trả: <span class="font-bold">{{ number_format($order->paid_amount) }} VNĐ</span></p>
        </div>
    </body>

</html>