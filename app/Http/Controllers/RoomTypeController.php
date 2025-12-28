<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\RoomType;
use App\Models\Branch;

class RoomTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roomTypeList = RoomType::with(['branches', 'rooms', 'images', 'amenities'])->get();
        $columns = [];

        if ($roomTypeList->isNotEmpty()) {
            $firstItem = $roomTypeList->first();
            $columns = array_keys($firstItem->getAttributes());
            $columns = array_diff($columns, ['id']);
        }

        $branchList = Branch::all();

        return Inertia::render('Admin/Room', [
            'roomTypeList' => $roomTypeList,
            'branchList'   => $branchList,
            'roomTypeColumns' => $columns,
            'activeTab'    => 'room-type',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'total_quantity' => 'integer|min:1|max:20',
            'max_adults' => 'integer|required',
            'max_children' => 'integer|required',
            'base_price' => 'required|numeric',
            'hourly_rate' => 'nullable|numeric|lt:overnight_rate',
            'overnight_rate' => 'nullable|numeric|gt:hourly_rate|lt:full_day_rate',
            'full_day_rate' => 'nullable|numeric|gt:overnight_rate',
            'status' => 'required|max:50',
        ]);

        $newRoomType = [
            'name' => $request->name,
            'description' => $request->description ?? '',
            'total_quantity' => $request->total_quantity,
            'max_adults' => $request->max_adults,
            'max_children' => $request->max_children,
            'base_price' => $request->base_price,
            'hourly_rate' => $request->hourly_rate,
            'full_day_rate' => $request->full_day_rate,
            'overnight_rate' => $request->overnight_rate,
            'status' => $request->status,
        ];

        // upload room type images
        $folderName = Str::slug($request->name); // "Deluxe Room" -> "deluxe-room"
        $path = "/uploads/room-type/{$folderName}";

        $imagePaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $filename = $file->getClientOriginalName(); // giữ nguyên tên file
                $storedPath = $file->storeAs($path, $filename, 'public'); // => storage/app/public/room-type/deluxe-room/filename.jpg
                $imagePaths[] = [
                    'path' => $storedPath,
                    'filename' => $filename
                ]; // lưu lại đường dẫn 
            }
        }

        // Tạo và gán lại biến model
        $newRoomTypeModel = RoomType::create($newRoomType);

        foreach ($imagePaths as $index => $img) {
            $newRoomTypeModel->images()->create([
                'path' => $img['path'],
                'is_featured' => $index === 0,
                'sort_order' => $index + 1,
                'alt_text' => $img['filename'],
            ]);
        }

        $branchIds = Branch::pluck('id');
        $inputBranchIds = $request->input('branch_id');

        if (!is_array($inputBranchIds)) {
            // Nếu là chuỗi có dấu phẩy "1,2,3" -> explode thành [1, 2, 3]
            if (is_string($inputBranchIds) && str_contains($inputBranchIds, ',')) {
                $inputBranchIds = explode(',', $inputBranchIds);
            } else {
                // Nếu là số đơn lẻ "1" -> ép thành mảng [1]
                $inputBranchIds = [$inputBranchIds];
            }
        }

        // Dùng values() để reset key mảng về 0,1,2... tránh lỗi key nhảy cóc
        $validBranchIds = collect($inputBranchIds)
            ->filter(fn($id) => $branchIds->contains($id))
            ->values();

        if ($validBranchIds->isNotEmpty()) {
            // Dùng sync sẽ an toàn hơn attach (tránh bị trùng lặp dữ liệu nếu chạy 2 lần)
            $newRoomTypeModel->branches()->sync($validBranchIds->toArray());
        }

        return redirect()->route('admin.room-type.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        dd($request);
        $validated = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'total_quantity' => 'min:1|max:20',
            'hourly_rate' => 'required|numeric|lt:overnight_rate',
            'full_day_rate' => 'required|numeric|gt:overnight_rate',
            'overnight_rate' => 'required|numeric|gt:hourly_rate|lt:full_day_rate',
            'status' => 'required|max:50',
        ]);

        $roomType = RoomType::find($id);
        $roomType->update($validated);

        // update các chi nhánh nếu có thay đổi
        $inputBranchIds = $request->input('branch_id');

        // 2. Xử lý chuẩn hóa Input về dạng Mảng (Array)
        // Logic: Nếu là chuỗi "1,2,3" -> tách thành mảng. Nếu là số lẻ -> gói vào mảng.
        if (!is_array($inputBranchIds)) {
            if (is_string($inputBranchIds) && str_contains($inputBranchIds, ',')) {
                $inputBranchIds = explode(',', $inputBranchIds);
            } else {
                // Trường hợp null hoặc số lẻ, ép về mảng (nếu null thì thành mảng rỗng [])
                $inputBranchIds = $inputBranchIds ? [$inputBranchIds] : [];
            }
        }

        // 3. Lọc ID hợp lệ
        // Thay vì pluck toàn bộ bảng Branch (rất nặng), ta chỉ check những ID user gửi lên
        $validBranchIds = Branch::whereIn('id', $inputBranchIds)->pluck('id')->toArray();

        // 4. Thực hiện Sync
        // sync sẽ làm cho bảng trung gian khớp hoàn toàn với $validBranchIds
        // (Cái nào thừa thì xóa, cái nào thiếu thì thêm)
        $roomType->branches()->sync($validBranchIds);
        return redirect()->route('admin.room-type.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $roomType = RoomType::with('images')->where('id', $id)->first();
        if ($roomType) {
            // Xóa từng ảnh vật lý trong storage
            foreach ($roomType->images as $image) {
                if (Storage::disk('public')->exists($image->path)) {
                    Storage::disk('public')->delete($image->path);
                }
            }
        }

        // Xóa thư mục chứa ảnh (nếu có)
        $folder = 'uploads/room-type/' . Str::slug($roomType->name);
        if (Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->deleteDirectory($folder);
        }

        // Xóa record ảnh trong DB
        $roomType->images()->delete();
        $roomType->delete();
        return redirect()->route('admin.room-type.index');
    }
}
