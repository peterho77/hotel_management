<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\RoomOptionRatePolicy;
use App\Models\RoomOption;
use App\Models\Room;
use App\Models\Customer;
use App\Models\Booking;
use App\Services\EmailSMTP\EmailService;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }
    public function index(Request $request)
    {
        $checkIn = $request->query('checkIn');
        $checkOut = $request->query('checkOut');

        $roomOptionList = RoomOptionRatePolicy::with(['room_option.room_type.amenities', 'rate_policy'])
            ->whereHas('room_option', function ($queryOption) use ($checkIn, $checkOut) {

                // 1. Đi vào RoomType
                $queryOption->whereHas('room_type', function ($queryType) use ($checkIn, $checkOut) {

                    // 2. RoomType phải Active
                    $queryType->where('status', 'active');

                    // 3. (QUAN TRỌNG) Kiểm tra xem có phòng vật lý nào THỰC SỰ TRỐNG không?
                    // Logic: Tìm các phòng (active) VÀ (không có booking nào trùng lịch)
                    $queryType->whereHas('rooms', function ($queryRoom) use ($checkIn, $checkOut) {

                        // 3a. Phòng phải đang Active (không bảo trì)
                        $queryRoom->where('status', 'active');

                        // 3b. Nếu khách có chọn ngày, lọc bỏ những phòng đã bị đặt
                        if ($checkIn && $checkOut) {
                            $queryRoom->whereDoesntHave('booking_items', function ($qItem) use ($checkIn, $checkOut) {
                                $qItem->whereHas('booking', function ($qBooking) use ($checkIn, $checkOut) {
                                    // Booking chưa bị hủy
                                    $qBooking->where('status', '!=', 'cancelled')
                                        // Logic check trùng lịch: (StartA < EndB) && (EndA > StartB)
                                        ->where(function ($sub) use ($checkIn, $checkOut) {
                                            $sub->where('check_in', '<', $checkOut)
                                                ->where('check_out', '>', $checkIn);
                                        });
                                });
                            });
                        }
                    });
                });
            })
            ->get();

        return Inertia::render('Guest/Booking', [
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'num_of_guests' => (int) $request->query('num_of_guests'),
            'num_of_rooms' => (int) $request->query('num_of_rooms'),
            'roomOptionList' => $roomOptionList
        ]);
    }

    public function detail(Request $request)
    {
        $roomBookingDetail = $request->validate([
            'date_range' => 'required',
            'num_adults' => 'required|integer',
            'num_children' => 'required|integer',
            'initial_num_rooms' => 'required|integer',
            'num_rooms' => 'required|integer',
            'num_nights' => 'required|integer',
            'selected_rooms' => 'required|array',
            'total_base_price' => 'required',
            'total_final_price' => 'required',
        ]);

        try {
            $checkIn = Carbon::createFromFormat('d/m/Y', $roomBookingDetail['date_range'][0])->format('Y-m-d');
            $checkOut = Carbon::createFromFormat('d/m/Y', $roomBookingDetail['date_range'][1])->format('Y-m-d');
        } catch (\Exception $e) {
            // Fallback nếu format sai hoặc dùng format khác
            $checkIn = Carbon::parse($roomBookingDetail['date_range'][0])->format('Y-m-d');
            $checkOut = Carbon::parse($roomBookingDetail['date_range'][1])->format('Y-m-d');
        }

        foreach ($roomBookingDetail['selected_rooms'] as &$item) {

            // Lấy room_type_id từ dữ liệu gửi lên
            // Cấu trúc: item -> room_option -> room_type_id
            $roomTypeId = $item['room_option']['room_type_id'] ?? null;

            if ($roomTypeId) {
                // Query tìm tầng có phòng trống
                $availableFloors = Room::where('room_type_id', $roomTypeId)
                    ->where('status', 'active') // Chỉ lấy phòng đang hoạt động
                    ->whereDoesntHave('booking_items', function ($query) use ($checkIn, $checkOut) {
                        // Loại bỏ các phòng đã dính booking trong khoảng ngày này
                        $query->whereHas('booking', function ($q) use ($checkIn, $checkOut) {
                            $q->where('status', '!=', 'cancelled')
                                ->where(function ($sub) use ($checkIn, $checkOut) {
                                    // Logic check trùng lịch: (CheckIn < RequestOut) AND (CheckOut > RequestIn)
                                    $sub->where('check_in', '<', $checkOut)
                                        ->where('check_out', '>', $checkIn);
                                });
                        });
                    })
                    ->select('floor')
                    ->distinct()            // Chỉ lấy các tầng duy nhất
                    ->orderBy('floor', 'asc') // Sắp xếp tăng dần
                    ->pluck('floor')        // Chỉ lấy mảng giá trị: [1, 2, 3]
                    ->toArray();


                $item['available_floors'] = $availableFloors;

                // Đảm bảo mảng selected_floor tồn tại (nếu null thì gán mảng rỗng)
                if (empty($item['selected_floor']) || is_null($item['selected_floor'][0])) {
                    // Khởi tạo mảng rỗng có độ dài bằng số lượng phòng đặt
                    $qty = $item['selected_quantity'] ?? 1;
                    $item['selected_floor'] = array_fill(0, $qty, null);
                }
            }
        }
        unset($item);

        return Inertia::render('Guest/Booking-infor', [
            'roomBookingDetail' => $roomBookingDetail
        ]);
    }

    public function confirm(Request $request)
    {
        $bookingInfor = $request->validate([
            'check_in' => 'required|date_format:d/m/Y',
            'check_out' => 'required|date_format:d/m/Y|after:check_in',
            'num_nights' => 'required|integer',
            'num_rooms' => 'required|integer',
            'num_adults' => 'required|integer',
            'num_children' => 'nullable|integer',
            'total_base_price' => 'required|numeric',
            'total_price' => 'required|numeric',

            // Customer Info
            'full_name' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'country' => 'nullable|string',
            'special_request' => 'nullable|string',

            // Room Options
            'selected_rooms' => 'required|array|min:1',
            'selected_rooms.*.room_option_id' => 'required|integer|exists:room_option,id',
            'selected_rooms.*.selected_quantity' => 'required|integer|min:1',

            // Validate mảng tầng
            'selected_rooms.*.selected_floor' => 'nullable|array',
            'selected_rooms.*.selected_floor.*' => 'integer',
        ]);

        // Validation logic nâng cao: Số lượng tầng chọn phải khớp số lượng phòng (nếu có chọn)
        foreach ($request->input('selected_rooms') as $index => $room) {
            if (isset($room['selected_floor']) && count($room['selected_floor']) > 0) {
                // Lọc bỏ giá trị null (phòng hờ)
                $floors = array_filter($room['selected_floor'], fn($v) => !is_null($v));
                if (count($floors) > 0 && count($floors) != $room['selected_quantity']) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        "selected_rooms.$index.selected_floor" => ["Số lượng tầng chọn phải bằng số lượng phòng đặt."]
                    ]);
                }
            }
        }

        // Format ngày
        $checkInDate = Carbon::createFromFormat('d/m/Y', $bookingInfor['check_in'])->format('Y-m-d');
        $checkOutDate = Carbon::createFromFormat('d/m/Y', $bookingInfor['check_out'])->format('Y-m-d');

        // Thêm các trường cần thiết
        $bookingInfor['check_in'] = $checkInDate;
        $bookingInfor['check_out'] = $checkOutDate;
        $bookingInfor['status'] = 'pending';
        $bookingInfor['payment_option'] = 'online';

        // --- Xử lý Customer ---
        if (Auth::check() && Auth::user()->customer) {
            $customer = Auth::user()->customer;
        } else {
            $customer = Customer::firstOrCreate(
                ['email' => $bookingInfor['email']],
                [
                    'full_name' => $bookingInfor['full_name'],
                    'phone' => $bookingInfor['phone'],
                    'country' => $bookingInfor['country'],
                    'user_id' => Auth::id() ?? null
                ]
            );
        }
        $bookingInfor['customer_id'] = $customer->id;

        // --- Bắt đầu Transaction (Closure style như code mẫu) ---
        return DB::transaction(function () use ($bookingInfor, $request, $checkInDate, $checkOutDate) {

            $newBooking = Booking::create($bookingInfor);
            $rawSelectedRooms = $request->input('selected_rooms', []);

            foreach ($rawSelectedRooms as $item) {
                $optionId = $item['room_option_id'];
                $quantityNeeded = $item['selected_quantity'];

                // Mảng tầng yêu cầu
                $requestedFloors = $item['selected_floor'] ?? [];

                $option = RoomOption::find($optionId);
                $roomTypeId = $option->room_type_id;

                // 1. Lấy TOÀN BỘ phòng trống (Sắp xếp tầng thấp -> cao để fallback hợp lý)
                $availableRooms = Room::where('room_type_id', $roomTypeId)
                    ->where('status', 'active')
                    ->whereDoesntHave('booking_items', function ($query) use ($checkInDate, $checkOutDate) {
                        $query->whereHas('booking', function ($q) use ($checkInDate, $checkOutDate) {
                            $q->where('status', '!=', 'cancelled')
                                ->where(function ($sub) use ($checkInDate, $checkOutDate) {
                                    $sub->where('check_in', '<', $checkOutDate)
                                        ->where('check_out', '>', $checkInDate);
                                });
                        });
                    })
                    ->orderBy('floor', 'asc') // Ưu tiên lấy tầng thấp trước nếu phải random
                    ->get();

                // Kiểm tra tổng quan xem có đủ phòng không
                if ($availableRooms->count() < $quantityNeeded) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'selected_rooms' => ["Loại phòng {$option->name} chỉ còn {$availableRooms->count()} phòng trống, không đủ {$quantityNeeded} phòng."]
                    ]);
                }

                // Tính giá
                $basePrice  = $option->price;
                $finalPrice = $option->final_price ?? $option->price;
                $totalBaseForOne = $basePrice  * $newBooking->num_nights;
                $totalFinalForOne = $finalPrice * $newBooking->num_nights;

                // 2. Vòng lặp để nhặt từng phòng
                for ($i = 0; $i < $quantityNeeded; $i++) {

                    $assignedRoom = null;
                    $targetFloor = $requestedFloors[$i] ?? null;

                    // --- LOGIC MỚI: Ưu tiên tầng chọn, không có thì lấy đại ---

                    if ($targetFloor) {
                        // Cố gắng tìm phòng ở đúng tầng khách chọn
                        $key = $availableRooms->search(function ($room) use ($targetFloor) {
                            return $room->floor == $targetFloor;
                        });

                        if ($key !== false) {
                            // Tìm thấy đúng tầng -> Lấy
                            $assignedRoom = $availableRooms->pull($key);
                        }
                    }

                    // Fallback: Nếu không tìm thấy phòng đúng tầng HOẶC khách không chọn tầng
                    // -> Lấy phòng đầu tiên trong danh sách (là phòng tầng thấp nhất do đã orderBy)
                    if (!$assignedRoom) {
                        $assignedRoom = $availableRooms->shift();
                    }

                    // Check an toàn cuối cùng
                    if (!$assignedRoom) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'selected_rooms' => ["Lỗi hệ thống: Không thể gán phòng cho loại {$option->name}."]
                        ]);
                    }

                    // Create Item
                    $newBooking->room_booking_items()->create([
                        'room_option_id'      => $optionId,
                        'assigned_room_id'    => $assignedRoom->id,
                        'total_base_price'    => $totalBaseForOne,
                        'discount_amount'     => $totalBaseForOne - $totalFinalForOne,
                        'final_price'         => $totalFinalForOne,
                    ]);
                }
            }

            // Tạo Payment
            $newBooking->payments()->create([
                'type' => 'e_wallet',
                'instrument' => strtolower($request->input('payment_instrument')),
                'status' => 'pending',
                'transaction_type' => 'full',
                'amount' => $newBooking->total_price,
            ]);

            return response()->json([
                'message' => 'Booking created successfully',
                'redirect_url' => route('payment.vnpay', ['bookingId' => $newBooking->id])
            ]);
        });
    }
}
