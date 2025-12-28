<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = "product";
    protected $fillable = [
        'code',
        'barcode',
        'name',
        'category',
        'min_inventory',
        'max_inventory',
        'cost_price',
        'selling_price',
        'weight',
        'location',
        'description',
        'note',
        'is_active',
    ];

    /**
     * Cast các trường dữ liệu về đúng định dạng
     */
    protected $casts = [
        'is_active' => 'boolean',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'min_inventory' => 'integer',
        'max_inventory' => 'integer',
    ];

    protected static function booted()
    {
        static::created(function ($product) {
            // Tạo mã: 'HH' nối với ID được đệm số 0 (độ dài 3 ký tự)
            $product->code = 'HH' . str_pad($product->id, 3, '0', STR_PAD_LEFT);
            
            // Lưu lại mà không kích hoạt thêm sự kiện để tránh vòng lặp
            $product->saveQuietly(); 
        });
    }
}
