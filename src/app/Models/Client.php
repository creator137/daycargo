<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'city',
        'client_type',
        'is_agent',
        'lang',
        'full_name',
        'birth_date',
        'phone',
        'email',
        'passport_series',
        'passport_number',
        'photo_path',
        'comment',
        'send_trip_report',
        'news_notifications',
        'allow_push',
        'blacklisted',
        'credit_limit',
        'balance',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_agent' => 'bool',
        'send_trip_report' => 'bool',
        'news_notifications' => 'bool',
        'allow_push' => 'bool',
        'blacklisted' => 'bool',
        'credit_limit' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function devices()
    {
        return $this->hasMany(ClientDevice::class);
    }


    public function getPhotoUrlAttribute(): ?string
    {

        return $this->photo_path ? Storage::url($this->photo_path) : null;
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_client')
            ->withPivot(['is_admin', 'active', 'personal_limit'])
            ->withTimestamps();
    }

    public function walletTransactions()
    {
        return $this->morphMany(\App\Models\WalletTransaction::class, 'owner')->latest();
    }

    public function bonusEntries()
    {
        return $this->hasMany(\App\Models\BonusEntry::class)->latest();
    }

    public function promoRedemptions()
    {
        return $this->hasMany(\App\Models\PromoCodeRedemption::class);
    }
    public function referrals()
    {
        return $this->hasMany(\App\Models\Referral::class, 'referrer_id');
    }
}
