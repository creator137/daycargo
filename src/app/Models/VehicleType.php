<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    protected $fillable = [
        'code',
        'name',
        'length_cm',
        'width_cm',
        'height_cm',
        'capacity_kg',
        'active',
        'sort',
        'body_height_from_ground_cm',
    ];

    protected $casts = [
        'length_cm'   => 'integer',
        'width_cm'    => 'integer',
        'height_cm'   => 'integer',
        'capacity_kg' => 'integer',
        'active'      => 'boolean',
        'sort'        => 'integer',
        'body_height_from_ground_cm' => 'integer',
    ];
}
