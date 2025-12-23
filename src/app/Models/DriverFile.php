<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverFile extends Model
{
    protected $fillable = [
        'driver_id',
        'type',
        'path',
        'original_name',
        'size',
        'mime',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
