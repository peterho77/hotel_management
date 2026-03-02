<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Service;
use App\Models\Product;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\CustomerGroup;
use App\Models\Employee;

class RetailController extends Controller
{
    //

    public function index()
    {
        $serviceList = Service::get();
        $productList = Product::get();
        $customerList = Customer::get();
        $customerTypeList = CustomerType::get();
        $customerGroupList = CustomerGroup::get();
        $employeeList = Employee::get();
        return Inertia::render('Employee/Retail', [
            'serviceList' => $serviceList,
            'productList' => $productList,
            'customerTypeList' => $customerTypeList,
            'customerGroupList' => $customerGroupList,
            'customerList' => $customerList,
            'employeeList' => $employeeList,
        ]);
    }
}
