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
        'partner_id',
        'partner_name',

        'payout_card',
        'payout_first_name_en',
        'payout_last_name_en',

        // есть в БД (раньше не было в fillable)
        'card_number',
        'card_holder_latin',
        'yandex_wallet',
        'sms_fixed_code',
        'sms_code',
        'cooperation_type',
        'options',
        'color',

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

        'is_loader',

        // НОВОЕ: документы (реквизиты)
        'passport_series',
        'passport_number',
        'passport_issued_by',
        'passport_issued_at',
        'passport_reg_address',
        'passport_fact_address',
        'inn',
        'ogrnip',
        'snils',
        'driver_license_series',
        'driver_license_number',
        'driver_license_category',
        'driver_license_experience_from',
        'driver_license_expires_at',
    ];

    protected $casts = [
        'supports_terminal' => 'boolean',
        'birth_date'        => 'date',
        'cities'            => 'array',
        'balance'           => 'decimal:2',
        'options'           => 'array',
        'is_loader' => 'boolean',

        // НОВОЕ: даты документов
        'passport_issued_at'             => 'date',
        'driver_license_experience_from' => 'date',
        'driver_license_expires_at'      => 'date',
    ];

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

    public function getDisplayNameAttribute(): string
    {
        if (!empty($this->full_name)) return $this->full_name;
        if (!empty($this->callsign)) return $this->callsign;
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
