<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Service;
use App\Models\Product;
use App\Models\Customer;

class ServiceOrderController extends Controller
{
    //

    public function index()
    {
        $serviceList = Service::get();
        $productList = Product::get();
        $customerList = Customer::get();
        return Inertia::render('Employee/ServiceOrder', [
            'serviceList' => $serviceList,
            'productList' => $productList,
            'customerList' => $customerList,
        ]);
    }
}
