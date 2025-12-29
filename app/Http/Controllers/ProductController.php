<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Service;

class ProductController extends Controller
{
    public function index()
    {
        $productList = Product::get();
        $serviceList = Service::get();

        return Inertia::render('Admin/Product', [
            'productList' => $productList,
            'serviceList' => $serviceList,
        ]);
    }
}
