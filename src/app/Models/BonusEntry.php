<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonusEntry extends Model
{
    protected $fillable = [
        'client_id',
        'type',
        'points',
        'source',
        'expires_at',
        'comment',
        'performed_by',
        'meta',
    ];

    protected $casts = [
        'points'     => 'decimal:2',
        'expires_at' => 'datetime',
        'meta'       => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
