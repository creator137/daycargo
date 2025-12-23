<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Driver extends Model
{
    protected $fillable = [
        'full_name',
        'callsign',
        'status',

        'vehicle_type_id',
        'driver_group_id',
        'supports_terminal',

        'phone',
        'email',
        'birth_date',

        'main_city',
        'cities',
        'partner_name',

        'payout_card',
        'payout_first_name_en',
        'payout_last_name_en',
        'yandex_wallet',

        'sms_fixed_code',
        'sort',
        'comment',

        'app_password',
        'avatar_path',

        'updated_by',
        'balance',

        'first_name',
        'last_name',
        'second_name',
        'citizenship',
        'employment_type',
        'city_id',
    ];

    protected $casts = [
        'supports_terminal' => 'boolean',
        'birth_date'        => 'date',
        'cities'            => 'array',
        'balance'           => 'decimal:2',
    ];

    // Отношения
    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function driverGroup()
    {
        return $this->belongsTo(DriverGroup::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Удобный аксессор — пригодится во вьюхах/таблицах
    public function getDisplayNameAttribute(): string
    {
        if (!empty($this->full_name)) {
            return $this->full_name;
        }
        if (!empty($this->callsign)) {
            return $this->callsign;
        }
        return (string) $this->phone;
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar_path) {
            return null;
        }
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        return $disk->url($this->avatar_path);
    }

    public function files()
    {
        return $this->hasMany(\App\Models\DriverFile::class);
    }
}
