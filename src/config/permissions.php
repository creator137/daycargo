<?php

return [
    'permissions' => [
        // Tariffs
        'tariffs.view',
        'tariffs.create',
        'tariffs.update',
        'tariffs.delete',
        'tariffs.toggle',

        // Dicts: Vehicle Types
        'dicts.vehicle_types.view',
        'dicts.vehicle_types.create',
        'dicts.vehicle_types.update',
        'dicts.vehicle_types.delete',
        'dicts.vehicle_types.toggle',

        // Reports (чтение)
        'reports.view',
    ],

    'roles' => [
        'owner' => ['*'],
        'admin' => [
            'tariffs.*',
            'dicts.vehicle_types.*',
            'reports.view',
        ],
        'accountant' => [
            'reports.view',
        ],
        'viewer' => [
            'tariffs.view',
            'dicts.vehicle_types.view',
            'reports.view',
        ],
        'driver' => [
            // без прав в админке
        ],
    ],
];
