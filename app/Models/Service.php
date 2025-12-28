<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'code',
        'name',
        'duration',
        'cost_price',
        'selling_price',
        'description',
        'note',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'duration' => 'integer',
    ];

    protected static function booted()
    {
        static::created(function ($service) {
            $service->code = 'DV' . str_pad($service->id, 3, '0', STR_PAD_LEFT);
            $service->saveQuietly();
        });
    }
}
