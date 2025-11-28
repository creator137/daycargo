<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TariffGroup extends Model
{
    protected $fillable = ['name', 'sort', 'description', 'active'];

    protected $casts = [
        'sort'   => 'integer',
        'active' => 'boolean',
    ];
}
