<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\Branch;

class RoomTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roomTypeList = RoomType::with(['branches', 'rooms', 'images'])->get();
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
            'total_quantity' => 'integer|min:1|max:10',
            'max_adults' => 'integer|required',
            'max_children' => 'integer|required',
            'hourly_rate' => 'required|numeric|lt:overnight_rate',
            'overnight_rate' => 'required|numeric|gt:hourly_rate|lt:full_day_rate',
            'full_day_rate' => 'required|numeric|gt:overnight_rate',
            'status' => 'required|max:50',
        ]);

        $newRoomType = [
            'name' => $request->name,
            'description' => $request->description ?? '',
            'total_quantity' => $request->total_quantity,
            'max_adults' => $request->max_adults,
            'max_children' => $request->max_children,
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

        //Lấy danh sách branch id hợp lệ
        $branchIds = Branch::pluck('id');

        // Lấy input từ request (nếu là mảng thì xử lý mảng, nếu 1 id thì xử lý 1 id)
        $inputBranchIds = $request->input('branch_id');

        if (is_array($inputBranchIds)) {
            // lọc các id hợp lệ
            $validBranchIds = collect($inputBranchIds)->filter(fn($id) => $branchIds->contains($id));

            if ($validBranchIds->isNotEmpty()) {
                $newRoomTypeModel->branches()->attach($validBranchIds->toArray());
            }
        } else {
            $branchId = (int)$inputBranchIds;
            if ($branchIds->contains($branchId)) {
                $newRoomTypeModel->branches()->attach($branchId);
            }
        }

        return redirect()->route('admin.room-type-management');
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
        $validated = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'quantity' => 'min:1|max:10',
            'hourly_rate' => 'required|numeric|lt:overnight_rate',
            'full_day_rate' => 'required|numeric|gt:overnight_rate',
            'overnight_rate' => 'required|numeric|gt:hourly_rate|lt:full_day_rate',
            'status' => 'required|max:50',
        ]);

        $roomType = RoomType::find($id);
        $roomType->update($validated);

        // update các chi nhánh nếu có thay đổi
        // Lấy danh sách branch id hợp lệ
        $branchIds = Branch::pluck('id');

        // Lấy input từ request là string 
        $inputBranchIds = $request->input('branch_id');

        if (is_array($inputBranchIds)) {
            // ép kiểu sang int tất cả phần tử
            $inputBranchIds = collect($inputBranchIds)->map(fn($id) => (int) $id);

            // lọc các id hợp lệ
            $validBranchIds = $inputBranchIds->filter(fn($id) => $branchIds->contains($id));

            if ($validBranchIds->isNotEmpty()) {
                $roomType->branches()->sync($validBranchIds->toArray());
            }
        } else {
            $branchId = (int)$inputBranchIds;
            if ($branchIds->contains($branchId)) {
                $roomType->branches()->sync($branchId);
            }
        }
        return redirect()->route('admin.room-type-management');
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
        return redirect()->route('admin.room-type-management');
    }
}
