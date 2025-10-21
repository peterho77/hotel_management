<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CustomerGroup;
use App\Models\CustomerType;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class CustomerController extends Controller
{
    //
    public function index()
    {
        $customersList = User::where('role', 'customer')->get();
        if ($customersList->isNotEmpty()) {
            $firstItem = $customersList->first();
            $columns = array_keys($firstItem->getAttributes());
            $columns = array_diff($columns, ['password', 'email_verified_at', 'remember_token','role']);
        };

        return Inertia::render('Admin/Partner', [
            'customersList' => $customersList,
            'columns' => $columns
        ]);
    }
}
