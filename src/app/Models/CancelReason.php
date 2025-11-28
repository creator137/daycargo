<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CancelReason extends Model
{
    public const INITIATORS = [
        'customer'    => 'Клиент',
        'driver'      => 'Водитель',
        'dispatcher'  => 'Диспетчер',
        'system'      => 'Система',
        'integration' => 'Интеграция',
    ];

    protected $fillable = [
        'code',
        'title',
        'initiator',
        'window_minutes',
        'client_fee_fixed',
        'client_fee_percent',
        'driver_fee_fixed',
        'driver_fee_percent',
        'driver_fee_min',
        'comment',
        'sort',
        'active',
    ];

    protected $casts = [
        'window_minutes'    => 'integer',
        'client_fee_fixed'  => 'decimal:2',
        'client_fee_percent' => 'decimal:2',
        'driver_fee_fixed'  => 'decimal:2',
        'driver_fee_percent' => 'decimal:2',
        'driver_fee_min'    => 'decimal:2',
        'sort'              => 'integer',
        'active'            => 'boolean',
    ];

    public static function initiatorOptions(): array
    {
        return self::INITIATORS;
    }

    public function getInitiatorLabelAttribute(): string
    {
        return self::INITIATORS[$this->initiator] ?? $this->initiator;
    }
}
