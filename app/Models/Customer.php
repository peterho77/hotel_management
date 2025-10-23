<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = "customer";
    protected $fillable = [
        'full_name',
        'user_id',
        'gender',
        'birth_date',
        'address',
        'email',
        'phone',
        'customer_type_id',
        'customer_group_id',
        'note',
        'has_account'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer_type()
    {
        return $this->belongsTo(CustomerType::class, 'customer_type_id');
    }

    public function customer_group()
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
    }

    protected static function booted()
    {
        static::saving(function ($customer) {
            // Nếu có user_id thì set has_account = true
            $customer->has_account = !is_null($customer->user_id);
        });
    }
}
