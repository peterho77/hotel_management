<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Branch;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roomList = Room::with(['branch', 'room_type'])
            ->get()
            ->makeHidden(['branch_id', 'room_type_id']);
        if ($roomList->isNotEmpty()) {
            $firstItem = $roomList->first();
            $columns = array_keys($firstItem->getAttributes());
            $columns = array_diff($columns, ['branch_id', 'room_type_id']);
        };
        $roomTypeList = RoomType::all();
        $branchList = Branch::all();

        return Inertia::render('Admin/Room', [
            'roomList' => $roomList,
            'roomTypeList' => $roomTypeList,
            'branchList'   => $branchList,
            'roomColumns'      => $columns,
            'activeTab'    => 'room',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'area' => 'required',
            'status' => 'required|max:50',
            'room_type_id' => 'required',
            'branch_id' => 'required'
        ]);

        $newRoom = [
            'name' => $request->name,
            'area' => $request->area,
            'status' => $request->status,
            'note' => $request->note,
            'room_type_id' => $request->room_type_id,
            'branch_id' => $request->branch_id
        ];

        Room::create($newRoom);

        return redirect()->route('admin.room.index');
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
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required',
            'area' => 'required',
            'status' => 'required|max:50',
            'room_type_id' => 'required',
            'branch_id' => 'required'
        ]);

        $room = Room::find($id);
        $room->update($validated);
        return redirect()->route('admin.room.index')->with('success', 'Record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $room = Room::where('id', $id)->first();
        $room->delete();
        return redirect()->route('admin.room.index')->with('success', 'Record deleted successfully.');
    }
}
