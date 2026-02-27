<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'total_quantity' => 'required|integer|min:1|max:20',
            'max_adults'     => 'required|integer|min:1',
            'max_children'   => 'required|integer|min:0',
            'base_price'     => 'required|numeric|min:0',
            'hourly_rate'    => 'nullable|numeric|lt:overnight_rate',
            'overnight_rate' => 'nullable|numeric|gt:hourly_rate|lt:full_day_rate',
            'full_day_rate'  => 'nullable|numeric|gt:overnight_rate',
            'status'         => 'required|string|max:50',
            'images'         => 'nullable|array',
            'images.*'       => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Validate từng ảnh
            'branch_id'      => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên loại phòng.',
            'base_price.required' => 'Vui lòng nhập giá cơ bản.',
            // Thêm các messages khác nếu cần...
        ];
    }
}
