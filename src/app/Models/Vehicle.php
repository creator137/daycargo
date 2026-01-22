<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'vehicle_type_id',
        'city',
        'owner_type',
        'is_rent',
        'brand',
        'model',
        'year',
        'color',
        'license_plate',
        'vin',
        'photo_path',
        'options',
        'status',
        'comment',
        'not_in_list',
        'external_car_class_id',
        'dimensions',
        'body_type_id',
        'loading_types',
    ];

    protected $casts = [
        'is_rent' => 'bool',
        'options' => 'array',
        'year'    => 'integer',
        'not_in_list' => 'bool',
        'dimensions'  => 'array',
        'loading_types' => 'array',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function bodyType()
    {
        return $this->belongsTo(\App\Models\VehicleBodyType::class, 'body_type_id');
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? \Illuminate\Support\Facades\Storage::url($this->photo_path) : null;
    }

    /** Скоупы для табов статусов */
    public function scopeStatus($q, string $status)
    {
        return $q->where('status', $status);
    }
}
