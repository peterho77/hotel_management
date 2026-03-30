<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetailItem extends Model
{
    protected $table = "retail_item";

    protected $fillable = [
        'order_id',
        'service_id',
        'product_id',
        'quantity',
        'price',
        'subtotal'
    ];
    public function order()
    {
        return $this->belongsTo(RetailOrder::class, 'order_id');
    }

    public function product()
    {   
        return $this->belongsTo(Product::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    protected static function booted()
    {
        static::saving(function ($retailItem) {
            $retailItem->subtotal = $retailItem->quantity * $retailItem->price;
        });
    }
}
