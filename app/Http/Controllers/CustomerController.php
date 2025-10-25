<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\CustomerType;
use Inertia\Inertia;

class CustomerController extends Controller
{
    //
    public function index()
    {
        $customersList = Customer::with(['customer_type', 'customer_group'])
            ->get()
            ->makeHidden(['customer_type_id', 'customer_group_id']);

        $columns = [];

        if ($customersList->isNotEmpty()) {
            $firstItem = $customersList->first();
            $columns = array_keys($firstItem->getAttributes());
            $columns = array_diff($columns, ['customer_type_id', 'customer_group_id', 'email_verified_at', 'remember_token']);
        };

        $customerGroupList = CustomerGroup::get();
        $customerTypeList = CustomerType::get();

        return Inertia::render('Manager/Customer', [
            'customersList' => $customersList,
            'customerGroupList' => $customerGroupList,
            'customerTypeList' => $customerTypeList,
            'columns' => $columns
        ]);
    }

    public function store(Request $request)
    {
        // dd($request);
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female,other'],
            'birth_date' => ['nullable', 'date'],
            'address' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'unique:customer,email'],
            'phone' => ['nullable', 'string', 'unique:customer,phone'],
            'customer_type_id' => ['required', 'exists:customer_type,id'],
            'customer_group_id' => ['required', 'exists:customer_group,id'],
            'note' => ['nullable', 'string'],
            'avatar' => 'nullable|image|max:2048|mimes:jpg,bmp,png',
        ]);
        // dd($validated);
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('uploads/customers', 'public');
        }
        
        // Tạo khách hàng mới
        Customer::create([
            'full_name'   => $validated['full_name'],
            'gender'  => $validated['gender'],
            'birth_date'  => $validated['birth_date'],
            'address'  => $validated['address'],
            'email'  => $validated['email'],
            'phone'  => $validated['phone'],
            'customer_type_id'  => $validated['customer_type_id'],
            'customer_group_id'  => $validated['customer_group_id'],
            'note' => $validated['note'],
            'avatar' => $avatarPath ? '/storage/' . $avatarPath : null,
        ]);

        return redirect()->route('manager.customer')->with('success', 'Thêm khách hàng thành công!');
    }
}
