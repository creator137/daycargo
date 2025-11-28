<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverGroup extends Model
{
    protected $fillable = [
        'name',
        'city',
        'profession',
        'vehicle_type_id',
        'priority',
        'sort',
        'description',
        'active',

        // новое:
        'visibility_mode',
        'visible_vehicle_type_ids',
    ];

    protected $casts = [
        'vehicle_type_id'         => 'integer',
        'priority'                => 'integer',
        'sort'                    => 'integer',
        'active'                  => 'boolean',
        'visible_vehicle_type_ids' => 'array',
    ];

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function clientTariffs()
    {
        return $this->belongsToMany(ClientTariff::class, 'driver_group_client_tariff')
            ->withTimestamps();
    }
}
