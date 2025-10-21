<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerType extends Model
{
    protected $fillable = ['code', 'name', 'description'];

    // Một loại khách hàng có nhiều nhóm
    public function groups()
    {
        return $this->hasMany(CustomerGroup::class);
    }

    // Một loại khách hàng có nhiều user
    public function customers()
    {
        return $this->hasMany(User::class);
    }
}
