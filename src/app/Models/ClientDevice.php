<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientDevice extends Model
{
    protected $fillable = [
        'client_id',
        'city',
        'platform',
        'device_model',
        'os_version',
        'push_token',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
