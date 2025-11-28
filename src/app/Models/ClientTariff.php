<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientTariff extends Model
{
    protected $fillable = [
        'name',
        'tariff_group_id',
        'vehicle_type_id',
        'city',
        'description',
        'available_site',
        'available_app',
        'available_dispatcher',
        'available_driver',
        'available_cabinet',
        'require_prepayment',
        'addresses_min',
        'sort',
        'active',
    ];

    protected $casts = [
        'tariff_group_id'     => 'integer',
        'vehicle_type_id'     => 'integer',
        'available_site'      => 'boolean',
        'available_app'       => 'boolean',
        'available_dispatcher' => 'boolean',
        'available_driver'    => 'boolean',
        'available_cabinet'   => 'boolean',
        'require_prepayment'  => 'boolean',
        'addresses_min'       => 'integer',
        'sort'                => 'integer',
        'active'              => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(TariffGroup::class, 'tariff_group_id');
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }
}
