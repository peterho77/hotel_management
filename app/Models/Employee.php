<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employee';
    protected $fillable = [
        'full_name',
        'gender',
        'user_id',
        'phone',
        'email',
        'address',
        'birth_date',
        'identity_number',
        'branch_id',
        'has_account',
        'position',
        'start_date',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'start_date'    => 'date',
        'has_account'   => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function schedules()
    {
        return $this->hasMany(EmployeeSchedule::class);
    }
}
