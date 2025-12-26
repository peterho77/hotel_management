<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $table = 'shift';
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'branch_id'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function schedules()
    {
        return $this->hasMany(EmployeeSchedule::class);
    }
}
