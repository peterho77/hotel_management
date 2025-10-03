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
        $roomList = Room::all();
        if ($roomList->isNotEmpty()) {
            $firstItem = $roomList->first();
            $columns = array_keys($firstItem->getAttributes());
            $columns = array_diff($columns, ['id', 'branch_id']);
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $roomList = Room::all();
        if ($roomList->isNotEmpty()) {
            $firstItem = $roomList->first();
            $columns = array_keys($firstItem->getAttributes());
            $columns = array_diff($columns, ['id', 'branch_id']);
        };
        $roomTypeList = RoomType::all();
        $branchList = Branch::all();
    }
}
