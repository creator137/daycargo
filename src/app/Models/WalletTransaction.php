<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'owner_type',
        'owner_id',
        'wallet',
        'operation',
        'amount',
        'currency',
        'comment',
        'performed_by',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'meta'   => 'array',
    ];

    public function owner()
    {
        return $this->morphTo();
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
