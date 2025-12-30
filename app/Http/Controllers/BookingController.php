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
        $roomOptionList = RoomOptionRatePolicy::with(['room_option.room_type.amenities', 'rate_policy'])
            ->whereHas('room_option', function ($query) {
                $query->where('available_quantity', '>', 0);
            })
            ->get();
        return Inertia::render('Guest/Booking', [
            'checkIn' => $request->query('checkIn'),
            'checkOut' => $request->query('checkOut'),
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

        return Inertia::render('Guest/Booking-infor', [
            'roomBookingDetail' => $roomBookingDetail
        ]);
    }

    public function confirm(Request $request)
    {
        // 1. Validation
        $bookingInfor = $request->validate([
            // ... các validate cũ giữ nguyên ...
            'selected_rooms' => 'required|array|min:1',
            'selected_rooms.*.room_option_id' => 'required|integer|exists:room_option,id',
            'selected_rooms.*.selected_quantity' => 'required|integer|min:1',

            // Validate mảng tầng: Phải là mảng và các phần tử là số nguyên
            'selected_rooms.*.floors' => 'nullable|array',
            'selected_rooms.*.floors.*' => 'integer',
        ]);

        // Validation logic nâng cao: Số lượng tầng chọn phải khớp số lượng phòng (Nếu có gửi tầng)
        foreach ($request->input('selected_rooms') as $index => $room) {
            if (isset($room['floors']) && count($room['floors']) != $room['selected_quantity']) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "selected_rooms.$index.floors" => ["Số lượng tầng chọn phải bằng số lượng phòng đặt ({$room['selected_quantity']} phòng)."]
                ]);
            }
        }

        // Format ngày
        $checkInDate = Carbon::createFromFormat('d/m/Y', $bookingInfor['check_in'])->format('Y-m-d');
        $checkOutDate = Carbon::createFromFormat('d/m/Y', $bookingInfor['check_out'])->format('Y-m-d');

        $bookingInfor['check_in'] = $checkInDate;
        $bookingInfor['check_out'] = $checkOutDate;
        $bookingInfor['status'] = 'pending';
        $bookingInfor['payment_option'] = 'online';

        // --- Xử lý Customer (Giữ nguyên) ---
        if (Auth::check() && Auth::user()->customer) {
            $customer = Auth::user()->customer;
        } else {
            $customerInfor = $request->validate([ /*...*/]);
            // ... logic tạo customer ...
            // (Giả sử đoạn này bạn đã có biến $customer)
        }
        $bookingInfor['customer_id'] = $customer->id;

        // --- Bắt đầu Transaction ---
        return DB::transaction(function () use ($bookingInfor, $request, $checkInDate, $checkOutDate) {

            $newBooking = Booking::create($bookingInfor);
            $rawSelectedRooms = $request->input('selected_rooms', []);

            foreach ($rawSelectedRooms as $item) {
                $optionId = $item['room_option_id'];
                $quantityNeeded = $item['selected_quantity'];

                // Mảng tầng yêu cầu: VD [1, 3] hoặc [2]
                // Nếu không gửi lên thì để mảng rỗng
                $requestedFloors = $item['floors'] ?? [];

                $option = RoomOption::find($optionId);
                $roomTypeId = $option->room_type_id;

                // 1. Lấy TOÀN BỘ phòng trống của loại này trong giai đoạn đó
                $availableRooms = Room::where('room_type_id', $roomTypeId)
                    ->where('status', '!=', 'maintenance')
                    ->whereDoesntHave('bookingItems', function ($query) use ($checkInDate, $checkOutDate) {
                        $query->whereHas('booking', function ($q) use ($checkInDate, $checkOutDate) {
                            $q->where('status', '!=', 'cancelled')
                                ->where(function ($sub) use ($checkInDate, $checkOutDate) {
                                    $sub->where('check_in', '<', $checkOutDate)
                                        ->where('check_out', '>', $checkInDate);
                                });
                        });
                    })->get(); // Lấy về Collection các phòng trống

                // Kiểm tra tổng quan xem có đủ phòng không đã
                if ($availableRooms->count() < $quantityNeeded) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'selected_rooms' => ["Loại phòng {$option->name} chỉ còn {$availableRooms->count()} phòng trống, không đủ {$quantityNeeded} phòng."]
                    ]);
                }

                // Tính giá
                $basePrice  = $option->price;
                $finalPrice = $option->final_price;
                $totalBaseForOne = $basePrice  * $newBooking->num_nights;
                $totalFinalForOne = $finalPrice * $newBooking->num_nights;

                // 2. Vòng lặp để nhặt từng phòng ra khỏi danh sách Available
                for ($i = 0; $i < $quantityNeeded; $i++) {

                    $assignedRoom = null;
                    $targetFloor = $requestedFloors[$i] ?? null; // Lấy tầng yêu cầu thứ i (VD: i=0 lấy tầng 1, i=1 lấy tầng 3)

                    if ($targetFloor) {
                        // Logic: Tìm trong list available phòng nào ở tầng targetFloor
                        // search() trả về key của phần tử đầu tiên thỏa mãn
                        $key = $availableRooms->search(function ($room) use ($targetFloor) {
                            return $room->floor == $targetFloor;
                        });

                        if ($key !== false) {
                            // Tìm thấy phòng đúng tầng -> Lấy phòng đó và xóa khỏi danh sách available (để vòng lặp sau ko lấy trùng)
                            $assignedRoom = $availableRooms->pull($key);
                        } else {
                            // Khách yêu cầu tầng X nhưng tầng X đã hết phòng -> Báo lỗi strict
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                'selected_rooms' => ["Không còn phòng trống ở tầng {$targetFloor} cho loại {$option->name}."]
                            ]);

                            // HOẶC: Nếu muốn linh động (hết tầng 1 thì lấy tầng khác) thì dùng dòng dưới:
                            // $assignedRoom = $availableRooms->shift(); 
                        }
                    } else {
                        // Nếu khách không yêu cầu tầng cụ thể -> Lấy phòng đầu tiên trong list
                        $assignedRoom = $availableRooms->shift();
                    }

                    // Create Item
                    $newBooking->room_booking_items()->create([
                        'room_option_id'      => $optionId,
                        'assigned_room_id'    => $assignedRoom->id, // Chắc chắn có ID vì logic check bên trên
                        'applied_discount_id' => null,
                        'total_base_price'    => $totalBaseForOne,
                        'discount_amount'     => $totalBaseForOne - $totalFinalForOne,
                        'final_price'         => $totalFinalForOne,
                    ]);
                }
            }

            // Tạo Payment
            $newBooking->payments()->create([
                'type' => 'e_wallet',
                'instrument' => 'vnpay',
                'status' => 'pending',
                'transaction_type' => 'full',
                'amount' => $newBooking->total_price,
            ]);

            return response()->json([
                'redirect_url' => route('payment.vnpay', $newBooking->id)
            ]);
        });
    }
}
