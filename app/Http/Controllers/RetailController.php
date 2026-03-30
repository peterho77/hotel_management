<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Service;
use App\Models\Product;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\CustomerGroup;
use App\Models\Employee;
use App\Models\RetailOrder;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\Enums\Format;
use Illuminate\Support\Facades\File;

class RetailController extends Controller
{
    //
    public function index()
    {
        $serviceList = Service::get();
        $productList = Product::get();
        $customerList = Customer::get();
        $customerTypeList = CustomerType::get();
        $customerGroupList = CustomerGroup::get();
        $employeeList = Employee::get();
        return Inertia::render('Employee/Retail', [
            'serviceList' => $serviceList,
            'productList' => $productList,
            'customerTypeList' => $customerTypeList,
            'customerGroupList' => $customerGroupList,
            'customerList' => $customerList,
            'employeeList' => $employeeList,
        ]);
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'creator_id'   => 'required|exists:employee,id',
            'created_at'   => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount'  => 'required|numeric|min:0',

            // Validate mảng items
            'items'              => 'required|array|min:1',

            // Nếu không có product_id thì BẮT BUỘC phải có service_id và ngược lại
            'items.*.service_id' => 'nullable|required_without:items.*.product_id|exists:service,id',
            'items.*.product_id' => 'nullable|required_without:items.*.service_id|exists:product,id',

            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.price'      => 'required|numeric|min:0',
        ]);
        DB::beginTransaction();
        try {
            $order = RetailOrder::create([
                'creator_id'   => $validated['creator_id'],
                'created_at'   => $validated['created_at'],
                'total_amount' => $validated['total_amount'],
                'paid_amount'  => $validated['paid_amount'],
            ]);

            foreach ($validated['items'] as $item) {
                $order->items()->create([
                    // Laravel sẽ lấy giá trị từ request, nếu trống sẽ là null
                    'service_id' => $item['service_id'] ?? null,
                    'product_id' => $item['product_id'] ?? null,
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                ]);
            }

            DB::commit();
            return redirect()->back()->with([
                'success' => 'Hóa đơn đã được lưu!',
                'new_order_id' => $order->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Lỗi hệ thống: ' . $e->getMessage()]);
        }
    }

    public function print($orderId)
    {
        $order = RetailOrder::with(['items.service', 'items.product', 'creator'])->findOrFail($orderId);

        $filename = "invoice-{$order->code}-" . now()->format('Y-m-d-His') . ".pdf";

        // Xác định đường dẫn thư mục và đảm bảo thư mục đó tồn tại
        $directory = public_path('pdf');
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Đường dẫn đầy đủ để lưu file
        $fullPath = $directory . '/' . $filename;

        // Thực hiện render và lưu
        return Pdf::view('pdfs.invoice', ['order' => $order])
            ->format(Format::A4)
            ->save($fullPath) // Lưu file vật lý vào ổ đĩa
            ->inline($filename); // Trả về trình duyệt để xem/in ngay lập tức
    }
}
