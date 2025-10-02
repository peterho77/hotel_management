<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\RoomType;
use App\Models\Branch;

class RoomTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roomTypeList = RoomType::all();
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
            'columns'      => $columns,
            'activeTab'    => 'room_type',
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
        //
    }
}
