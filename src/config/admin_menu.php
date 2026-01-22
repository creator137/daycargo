<?php

return [
    ['label' => 'Главная', 'icon' => '🏠', 'route' => 'admin.dashboard'],

    [
        'label' => 'Заказы',
        'icon'  => '📦',
        'route' => 'admin.orders.index',
    ],

    [
        'label' => 'Клиенты',
        'icon'  => '👤',
        'children' => [
            ['label' => 'База клиентов', 'route' => 'admin.clients.index'],
            ['label' => 'Организации',   'route' => 'admin.clients.orgs'],
            ['label' => 'Бонусная система', 'route' => 'admin.loyalty.index', 'can' => 'promocodes.view'],
        ],
    ],

    [
        'label' => 'Исполнители',
        'icon'  => '🚚',
        'children' => [
            ['label' => 'Водители',        'route' => 'admin.drivers.index'],
            ['label' => 'Автомобили',      'route' => 'admin.vehicles.index'],
            //['label' => 'Тарифы водителей', 'route' => 'admin.drivers.tariffs'],
            ['label' => 'Группы водителей', 'route' => 'admin.driver_groups.index', 'can' => 'driver_groups.view'],
            //['label' => 'Фото-контроль',   'route' => 'admin.drivers.photo'],
            //['label' => 'Импорт',          'route' => 'admin.drivers.import'],
            //['label' => 'Бонусы',          'route' => 'admin.drivers.bonuses'],
        ],
    ],

    // отдельные разделы без подменю
    ['label' => 'Тарифы',     'icon' => '💸', 'route' => 'admin.tariffs.index'],
    [
        'label' => 'Тарифы для клиентов',
        'icon'  => '🏷️',
        'route' => 'admin.client_tariffs.index',
        'can'   => 'client_tariffs.view',
    ],

    ['label' => 'Интеграции', 'icon' => '🔗', 'route' => 'admin.integrations.index'],

    [
        'label' => 'Отчёты',
        'icon'  => '📈',
        'children' => [
            ['label' => 'Сводка', 'route' => 'admin.reports.summary'],
        ],
    ],

    [
        'label' => 'Справочники',
        'icon'  => '📚',
        'children' => [
            //['label' => 'Услуги',         'route' => 'admin.dicts.services'],
            ['label' => 'Типы авто',      'route' => 'admin.dicts.vehicle_types'],
            ['label' => 'Причины отмены', 'route' => 'admin.dicts.cancel_reasons'],
            [
                'label' => 'Города',
                'route' => 'admin.dicts.cities',
                'can'   => 'cities.view',
            ],
            [
                'label' => 'Группы тарифов',
                'route' => 'admin.dicts.tariff_groups',
                'can'   => 'tariff_groups.view',
            ],
            [
                'label' => 'Типы кузова',
                'route' => 'admin.dicts.vehicle_body_types',
                'can'   => 'role:owner', // если твой рендерер умеет так — см. ниже
            ],
            [
                'label' => 'Виды погрузки',
                'route' => 'admin.dicts.vehicle_loading_types',
                'can'   => 'role:owner',
            ],

        ],
    ],

    [
        'label' => 'Настройки',
        'icon'  => '⚙️',
        'children' => [
            ['label' => 'Статусы заказа', 'route' => 'admin.settings.statuses'],
        ],
    ],
];
