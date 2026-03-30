<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetailOrder extends Model
{
    protected $table = "retail_order";
    protected $fillable = [
        'code',
        'customer_id',
        'creator_id',
        'total_amount',
        'paid_amount',
        'created_at',
        'source_type'
    ];
    public function creator()
    {
        return $this->belongsTo(Employee::class, 'creator_id');
    }
    public function items()
    {
        return $this->hasMany(RetailItem::class, 'order_id');
    }

    protected static function booted()
    {
        static::created(function ($order) {
            // Tạo mã: 'HH' nối với ID được đệm số 0 (độ dài 3 ký tự)
            $order->code = 'HD' . str_pad($order->id, 5, '0', STR_PAD_LEFT);

            // Lưu lại mà không kích hoạt thêm sự kiện để tránh vòng lặp
            $order->saveQuietly();
        });
    }
}
