<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSchedule extends Model
{
    protected $table = "employee_schedule";

    protected $fillable = ['employee_id', 'shift_id', 'schedule_date', 'note'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
