<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'referrer_id',
        'referee_id',
        'status',
        'reward_points',
        'approved_at',
        'meta',
    ];

    protected $casts = [
        'reward_points' => 'decimal:2',
        'approved_at'   => 'datetime',
        'meta'          => 'array',
    ];

    public function referrer()
    {
        return $this->belongsTo(Client::class, 'referrer_id');
    }
    public function referee()
    {
        return $this->belongsTo(Client::class, 'referee_id');
    }
}
