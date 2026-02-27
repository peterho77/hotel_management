<?php

namespace App\Services;

use App\Models\RoomType;
use App\Models\Branch;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class RoomTypeService
{
    /**
     * Create a new class instance.
     */
    protected $roomType;
    public function __construct(RoomType $roomType)
    {
        $this->roomType = $roomType;
    }
    public function getAllData()
    {
        $roomTypeList = $this->roomType
            ::with(['branches', 'rooms', 'images', 'amenities'])
            ->get();

        $columns = [];
        if ($roomTypeList->isNotEmpty()) {
            $columns = array_diff(
                array_keys($roomTypeList->first()->getAttributes()),
                ['id']
            );
        }

        return [
            'roomTypeList' => $roomTypeList,
            'branchList'   => Branch::all(),
            'roomTypeColumns'  => array_values($columns),
        ];
    }
    private function handleImages(RoomType $roomType, array $images)
    {
        $folderName = Str::slug($roomType->name);
        $path = "/uploads/room-type/{$folderName}";

        foreach ($images as $index => $file) {
            if ($file instanceof UploadedFile) {
                $filename = $file->getClientOriginalName();
                $storedPath = $file->storeAs($path, $filename, 'public');

                $roomType->images()->create([
                    'path'        => $storedPath,
                    'is_featured' => $index === 0,
                    'sort_order'  => $index + 1,
                    'alt_text'    => $filename,
                ]);
            }
        }
    }
    private function cleanUpRoomTypeFiles(string $roomTypeName, $images)
    {
        // 1. Xóa từng file ảnh
        foreach ($images as $image) {
            if (Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }
        }

        // 2. Xóa toàn bộ thư mục (bao gồm các file rác phát sinh nếu có)
        $folderPath = "/uploads/room-type/" . Str::slug($roomTypeName);
        if (Storage::disk('public')->exists($folderPath)) {
            Storage::disk('public')->deleteDirectory($folderPath);
        }
    }

    private function syncBranches(RoomType $roomType, $inputBranchIds)
    {
        $branchIds = Branch::pluck('id');

        if (!is_array($inputBranchIds)) {
            $inputBranchIds = (is_string($inputBranchIds) && str_contains($inputBranchIds, ','))
                ? explode(',', $inputBranchIds)
                : [$inputBranchIds];
        }

        $validBranchIds = collect($inputBranchIds)
            ->filter(fn($id) => $branchIds->contains($id))
            ->values();

        if ($validBranchIds->isNotEmpty()) {
            $roomType->branches()->sync($validBranchIds->toArray());
        }
    }
    public function createRoomType(array $data, array $images = [], $inputBranchIds = null)
    {
        return DB::transaction(function () use ($data, $images, $inputBranchIds) {

            // 1. Tạo bản ghi mới
            $newRoomType = $this->roomType::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? '',
                'total_quantity' => $data['total_quantity'],
                'max_adults' => $data['max_adults'],
                'max_children' => $data['max_children'],
                'base_price' => $data['base_price'],
                'hourly_rate' => $data['hourly_rate'] ?? null,
                'full_day_rate' => $data['full_day_rate'] ?? null,
                'overnight_rate' => $data['overnight_rate'] ?? null,
                'status' => $data['status'],
            ]);

            // 2. Upload và lưu images
            if (!empty($images)) {
                $this->handleImages($newRoomType, $images);
            }

            // 3. Sync branches
            if ($inputBranchIds) {
                $this->syncBranches($newRoomType, $inputBranchIds);
            }

            return $newRoomType;
        });
    }

    public function updateRoomType(string $id, array $data, $inputBranchIds = null)
    {
        return DB::transaction(function () use ($id, $data, $inputBranchIds) {
            $roomType = $this->roomType::findOrFail($id);
            $roomType->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? '',
                'total_quantity' => $data['total_quantity'],
                'max_adults' => $data['max_adults'],
                'max_children' => $data['max_children'],
                'base_price' => $data['base_price'],
                'hourly_rate' => $data['hourly_rate'] ?? null,
                'full_day_rate' => $data['full_day_rate'] ?? null,
                'overnight_rate' => $data['overnight_rate'] ?? null,
                'status' => $data['status'],
            ]);

            if ($inputBranchIds) {
                $this->syncBranches($roomType, $inputBranchIds);
            }

            return $roomType;
        });
    }

    public function deleteRoomType(string $id)
    {
        // Load images trước để lấy dữ liệu đường dẫn file
        $roomType = $this->roomType->with('images')->findOrFail($id);

        // Lưu lại tên và danh sách ảnh vào biến tạm trước khi bản ghi bị xóa mất
        $roomTypeName = $roomType->name;
        $imagesSnapshot = $roomType->images;

        return DB::transaction(function () use ($roomType, $roomTypeName, $imagesSnapshot) {
            // 1. Gỡ liên kết chi nhánh
            $roomType->branches()->detach();

            // 2. Xóa bản ghi ảnh trong DB
            $roomType->images()->delete();

            // 3. Xóa chính nó
            $isDeleted = $roomType->delete();

            // 4. CHỈ KHI XÓA THÀNH CÔNG TRONG DB MỚI XÓA FILE
            if ($isDeleted) {
                $this->cleanUpRoomTypeFiles($roomTypeName, $imagesSnapshot);
            }

            return $isDeleted;
        });
    }
}
