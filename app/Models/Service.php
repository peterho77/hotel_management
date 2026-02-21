<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'service';
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
        'quantity' => 'integer',
        'is_active' => 'boolean',
        'cost_price' => 'float',
        'selling_price' => 'float',
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
