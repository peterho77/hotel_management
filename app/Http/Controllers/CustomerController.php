<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\CustomerType;
use Inertia\Inertia;

class CustomerController extends Controller
{
    //
    public function index()
    {
        $customersList = User::with(['customer_type', 'customer_group'])
            ->where('role', 'customer')
            ->get()
            ->makeHidden(['customer_type_id', 'customer_group_id']);

        $columns = [];

        if ($customersList->isNotEmpty()) {
            $firstItem = $customersList->first();
            $columns = array_keys($firstItem->getAttributes());
            $columns = array_diff($columns, ['customer_type_id', 'customer_group_id', 'password', 'email_verified_at', 'remember_token', 'role']);
        };

        $customerGroupList = CustomerGroup::get();
        $customerTypeList = CustomerType::get();

        return Inertia::render('Admin/Partner', [
            'customersList' => $customersList,
            'customerGroupList' => $customerGroupList,
            'customerTypeList' => $customerTypeList,
            'columns' => $columns
        ]);
    }
}
