<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;
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

    protected static function booted()
    {
        static::created(function ($employee) {
            // Tạo mã: 'NV' nối với ID được đệm số 0 (độ dài 3 ký tự)
            $employee->code = 'NV' . str_pad($employee->id, 3, '0', STR_PAD_LEFT);
            
            // Lưu lại mà không kích hoạt thêm sự kiện để tránh vòng lặp
            $employee->saveQuietly(); 
        });
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedules()
    {
        return $this->hasMany(EmployeeSchedule::class);
    }
}
