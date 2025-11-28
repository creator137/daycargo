<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    // области действия (внутренние коды) + русские подписи
    public const SCOPE_TYPES = [
        'global'      => 'Глобально',
        'customer'    => 'Клиент',
        'integration' => 'Интеграция',
    ];

    protected $fillable = [
        'vehicle_type_id',
        'scope_type',
        'scope_id',
        'city',
        'base_price',
        'per_km',
        'per_min',
        'min_price',
        'wait_free_min',
        'wait_per_min',
        'active',
    ];

    protected $casts = [
        'base_price'    => 'decimal:2',
        'per_km'        => 'decimal:2',
        'per_min'       => 'decimal:2',
        'min_price'     => 'decimal:2',
        'wait_per_min'  => 'decimal:2',
        'wait_free_min' => 'integer',
        'active'        => 'boolean',
    ];

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    // удобный метод для select
    public static function scopeTypeOptions(): array
    {
        return self::SCOPE_TYPES;
    }

    // русская подпись текущего поля
    public function getScopeTypeLabelAttribute(): string
    {
        return self::SCOPE_TYPES[$this->scope_type] ?? $this->scope_type;
    }
}
