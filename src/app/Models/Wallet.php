<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $fillable = [
        'owner_type',
        'owner_id',
        'type',
        'currency',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function bonusEntries(): HasMany
    {
        return $this->hasMany(BonusEntry::class);
    }
}
