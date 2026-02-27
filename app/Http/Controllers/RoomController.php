<?php

namespace App\Http\Controllers;

use App\Services\RoomService;
use App\Http\Requests\StoreRoomRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $roomService;

    public function __construct(RoomService $roomService)
    {
        $this->roomService = $roomService;
    }

    public function index()
    {
        $data = $this->roomService->getAllData();

        return Inertia::render(
            'Admin/Room',
            array_merge($data, [
                'activeTab' => 'room'
            ])
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomRequest $request)
    {
        $validatedData = $request->validated();

        $this->roomService->createRoom($validatedData);

        return redirect()->route('admin.room.index')
            ->with('success', 'Thêm phòng mới thành công.');;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreRoomRequest $request, string $id)
    {
        $validatedData = $request->validated();

        $this->roomService->updateRoom($id, $validatedData);

        return redirect()->route('admin.room.index')
            ->with('success', 'Record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->roomService->deleteRoom($id);

        return redirect()->route('admin.room.index')
            ->with('success', 'Record deleted successfully.');
    }
}
