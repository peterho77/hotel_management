<?php

namespace App\Http\Controllers;

use App\Services\RoomTypeService;
use App\Http\Requests\StoreRoomTypeRequest;
use Inertia\Inertia;

class RoomTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $roomTypeService;
    public function __construct(RoomTypeService $roomTypeService)
    {
        $this->roomTypeService = $roomTypeService;
    }
    public function index()
    {
        $data = $this->roomTypeService->getAllData();

        return Inertia::render(
            'Admin/Room',
            array_merge($data, ['activeTab' => 'room-type'])
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomTypeRequest $request)
    {
        $validatedData = $request->validated();

        $images = $request->file('images') ?? [];
        $branchIds = $request->input('branch_id');

        $this->roomTypeService->createRoomType($validatedData, $images, $branchIds);

        return redirect()->route('admin.room-type.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreRoomTypeRequest $request, string $id)
    {
        $validated = $request->validated();

        $this->roomTypeService->updateRoomType($id, $validated, $request->input('branch_id'));

        return redirect()->route('admin.room-type.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->roomTypeService->deleteRoomType($id);
        
        return redirect()->route('admin.room-type.index');
    }
}
