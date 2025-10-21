<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{
    protected $fillable = ['code', 'name', 'customer_type_id', 'discount_percent', 'description'];

    // Một nhóm thuộc về một loại khách hàng
    public function type()
    {
        return $this->belongsTo(CustomerType::class, 'customer_type_id');
    }

    // Một nhóm có thể có nhiều khách hàng
    public function customers()
    {
        return $this->hasMany(User::class);
    }
}
