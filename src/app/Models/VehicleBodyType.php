<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleBodyType extends Model
{
    protected $fillable = ['name', 'slug', 'sort', 'active'];

    protected $casts = [
        'active' => 'bool',
        'sort'   => 'int',
    ];
}
