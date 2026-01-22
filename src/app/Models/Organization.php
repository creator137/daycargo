<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'city',
        'full_name',
        'short_name',
        'legal_address',
        'postal_address',
        'director_name',
        'director_position',
        'chief_accountant',
        'inn',
        'kpp',
        'ogrn',
        'bank_name',
        'bank_account',
        'bank_corr',
        'bank_bik',
        'phone',
        'email',
        'site',
        'contact_person',
        'contact_position',
        'contact_phone',
        'contact_email',
        'contract_number',
        'contract_from',
        'contract_to',
        'billing_period_months',
        'credit_limit',
        'active',
        'balance',
        'comment',
        'edo_code',
    ];

    protected $casts = [
        'contract_from' => 'date',
        'contract_to'   => 'date',
        'active'        => 'bool',
        'credit_limit'  => 'decimal:2',
        'balance'       => 'decimal:2',
    ];

    public function employees()
    {
        return $this->belongsToMany(Client::class, 'organization_client')
            ->withPivot(['is_admin', 'active', 'personal_limit'])
            ->withTimestamps();
    }

    public function transactions()
    {
        return $this->hasMany(OrgTransaction::class);
    }

    /** scope для табов */
    public function scopeActive($q, bool $flag = true)
    {
        return $q->where('active', $flag);
    }

    public function wallets(): MorphMany
    {
        return $this->morphMany(\App\Models\Wallet::class, 'owner');
    }

    public function cashWallet(): ?\App\Models\Wallet
    {
        return $this->wallets()->where('type', 'cash')->first();
    }

    public function bonusWallet(): ?\App\Models\Wallet
    {
        return $this->wallets()->where('type', 'bonus')->first();
    }
}
