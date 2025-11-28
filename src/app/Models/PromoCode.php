<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromoCode extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'starts_at',
        'expires_at',
        'per_user_limit',
        'usage_limit',
        'active',
        'meta',
    ];

    protected $casts = [
        'value'        => 'decimal:2',
        'starts_at'    => 'datetime',
        'expires_at'   => 'datetime',
        'active'       => 'bool',
        'meta'         => 'array',
    ];

    public function redemptions(): HasMany
    {
        return $this->hasMany(PromoCodeRedemption::class);
    }

    public function scopeActive($q)
    {
        return $q->where('active', true)
            ->when(now(), fn($qq) => $qq->where(function ($w) {
                $w->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            }))->where(function ($w) {
                $w->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }
}
