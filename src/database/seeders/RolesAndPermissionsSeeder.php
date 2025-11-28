<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'web';

        // 1) Полный список прав
        $perms = [

            'acl.manage',

            // Тарифы расчётные
            'tariffs.view',
            'tariffs.create',
            'tariffs.update',
            'tariffs.delete',
            'tariffs.toggle',

            // Типы авто
            'dicts.vehicle_types.view',
            'dicts.vehicle_types.create',
            'dicts.vehicle_types.update',
            'dicts.vehicle_types.delete',
            'dicts.vehicle_types.toggle',

            // Причины отмены
            'dicts.cancel_reasons.view',
            'dicts.cancel_reasons.create',
            'dicts.cancel_reasons.update',
            'dicts.cancel_reasons.delete',
            'dicts.cancel_reasons.toggle',

            // Группы тарифов
            'tariff_groups.view',
            'tariff_groups.create',
            'tariff_groups.update',
            'tariff_groups.delete',
            'tariff_groups.toggle',

            // Тарифы для клиентов
            'client_tariffs.view',
            'client_tariffs.create',
            'client_tariffs.update',
            'client_tariffs.delete',
            'client_tariffs.toggle',

            // Группы водителей
            'driver_groups.view',
            'driver_groups.create',
            'driver_groups.update',
            'driver_groups.delete',
            'driver_groups.toggle',

            // Водители
            'drivers.view',
            'drivers.create',
            'drivers.update',
            'drivers.delete',
            'drivers.toggle',

            // Автомобили (на будущее)
            'vehicles.view',
            'vehicles.create',
            'vehicles.update',
            'vehicles.delete',
            'vehicles.toggle',

            // Отчёты
            'reports.view',

            'cities.view',
            'cities.create',
            'cities.update',
            'cities.delete',
            'cities.toggle',

            'clients.view',
            'clients.create',
            'clients.update',
            'clients.delete',
            'clients.toggle',

            'organizations.view',
            'organizations.create',
            'organizations.update',
            'organizations.delete',
            'organizations.toggle',
            'organizations.balance',
            'organizations.employees',

            // Клиентская экономика (баланс/бонусы/промокоды — UI вкладки)
            'client_economy.view',
            'client_economy.topup',
            'client_economy.debit',

            // Экономика организаций (баланс/бонусы)
            'org_economy.view',
            'org_economy.topup',
            'org_economy.debit',

            'promocodes.view',
            'promocodes.create',
            'promocodes.update',
            'promocodes.delete',
            'promocodes.apply',
            'referrals.view',
            'referrals.manage',

            'orders.view',
            'orders.create',
            'orders.update',
            'orders.delete',
            'orders.assign',
            'orders.search',
            'orders.cancel',
            'orders.complete',
            'orders.finance',
            'orders.attachments',
            'orders.map',
        ];

        // Создать permissions (если нет)
        foreach ($perms as $p) {
            Permission::findOrCreate($p, $guard);
        }

        // Русские названия секций (групп)
        $sectionRu = [
            'acl'                     => 'Администрирование',
            'cities'                  => 'Города',
            'client_tariffs'          => 'Тарифы для клиентов',
            'clients'                 => 'Клиенты',
            'dicts.vehicle_types'     => 'Справочники — Типы авто',
            'dicts.cancel_reasons'    => 'Справочники — Причины отмены',
            'tariff_groups'           => 'Группы тарифов',
            'tariffs'                 => 'Расчётные тарифы',
            'driver_groups'           => 'Группы исполнителей',
            'drivers'                 => 'Исполнители (водители)',
            'vehicles'                => 'Автомобили',
            'reports'                 => 'Отчёты',
            'organizations'           => 'Организации',
            'client_economy' => 'Клиентская экономика',
            'org_economy'    => 'Экономика организаций',
            'promocodes' => 'Промокоды',
            'referrals'  => 'Рефералы',
            'orders' => 'Заказы',
        ];

        // Словарь для действий
        $actionRu = [
            'view'   => 'Просмотр',
            'create' => 'Создание',
            'update' => 'Изменение',
            'delete' => 'Удаление',
            'toggle' => 'Вкл/Выкл',
            'manage' => 'Управление',
        ];

        // Присвоим display_name и section всем permissions
        $allPerms = Permission::where('guard_name', $guard)->get();
        foreach ($allPerms as $p) {
            // Вычисляем ключ секции
            $name = $p->name; // напр. dicts.vehicle_types.view
            $parts = explode('.', $name);

            // ключ секции (pref): dicts.vehicle_types / либо первый сегмент
            $pref = count($parts) >= 2 && $parts[0] === 'dicts'
                ? $parts[0] . '.' . $parts[1]
                : $parts[0];

            $section = $sectionRu[$pref] ?? mb_convert_case(str_replace(['_', '-', '.'], ' ', $pref), MB_CASE_TITLE, 'UTF-8');

            // русское действие
            $suffix = end($parts);
            $action = $actionRu[$suffix] ?? mb_convert_case($suffix, MB_CASE_TITLE, 'UTF-8');

            // если есть точное соответствие — сделаем чуть красивее
            $entityRu = $section; // «Типы авто — …», «Города — …» и т.п.
            $display = $p->display_name
                ?? ($name === 'acl.manage'
                    ? 'Управление доступом (ACL)'
                    : $entityRu . ': ' . $action);

            $p->display_name = $display;
            $p->section      = $section;
            $p->save();
        }


        // 2) Роли → права (маски разворачиваем)
        $map = [
            'owner'      => ['*'],
            'admin'      => [
                'acl.manage',
                'tariffs.*',
                'dicts.vehicle_types.*',
                'dicts.cancel_reasons.*',
                'tariff_groups.*',
                'client_tariffs.*',
                'driver_groups.*',
                'drivers.*',
                'vehicles.*',
                'reports.view',
                'clients.*',
                'client_economy.*',
                'org_economy.*',
                'promocodes.*',
                'referrals.*',
                'orders.*'
            ],
            'accountant' => [
                'reports.view',
                'tariffs.view',
                'client_tariffs.view',
                'dicts.vehicle_types.view',
                'dicts.cancel_reasons.view',
                'tariff_groups.view',
                'driver_groups.view',
                'drivers.view',
                'vehicles.view',
                'clients.view',
                'client_economy.view',
                'client_economy.topup',
                'client_economy.debit',
                'org_economy.view',
                'org_economy.topup',
                'org_economy.debit',
                'promocodes.view',
                'promocodes.apply',
                'referrals.view',
                'orders.view',
                'orders.finance'

            ],
            'viewer'     => [
                'reports.view',
                'tariffs.view',
                'client_tariffs.view',
                'dicts.vehicle_types.view',
                'dicts.cancel_reasons.view',
                'tariff_groups.view',
                'driver_groups.view',
                'drivers.view',
                'vehicles.view',
                'clients.view',
                'client_economy.view',
                'org_economy.view',
                'promocodes.view',
                'referrals.view',
                'orders.view',
                'orders.map'
            ],


        ];

        $roleLabels = [
            'owner'      => ['display_name' => 'Владелец',      'description' => 'Полный доступ ко всем функциям'],
            'admin'      => ['display_name' => 'Администратор', 'description' => 'Управление справочниками и сущностями'],
            'accountant' => ['display_name' => 'Бухгалтер',     'description' => 'Отчёты и финансы'],
            'viewer'     => ['display_name' => 'Наблюдатель',   'description' => 'Только просмотр'],
            // если нужны ещё:
            'driver'     => ['display_name' => 'Водитель',      'description' => 'Роль для мобильного приложения'],
            'executor'   => ['display_name' => 'Исполнитель',   'description' => 'Исполнитель услуг'],
            'test'       => ['display_name' => 'Тестовая',      'description' => 'Для испытаний'],
        ];

        // Создание ролей с метками
        foreach ($roleLabels as $sysName => $meta) {
            $r = Role::firstOrCreate(['name' => $sysName, 'guard_name' => $guard]);
            $r->fill($meta)->save();
        }

        // Метки и группы для прав
        $permMeta = [
            // группа «Справочники»
            'dicts.vehicle_types.view'   => ['display_name' => 'Типы авто: просмотр',      'group' => 'Справочники'],
            'dicts.vehicle_types.create' => ['display_name' => 'Типы авто: создание',      'group' => 'Справочники'],
            'dicts.vehicle_types.update' => ['display_name' => 'Типы авто: редактирование', 'group' => 'Справочники'],
            'dicts.vehicle_types.delete' => ['display_name' => 'Типы авто: удаление',      'group' => 'Справочники'],
            'dicts.vehicle_types.toggle' => ['display_name' => 'Типы авто: включить/выкл.', 'group' => 'Справочники'],

            'dicts.cancel_reasons.view'   => ['display_name' => 'Причины отмены: просмотр', 'group' => 'Справочники'],
            // ... по аналогии

            // группа «Тарифы»
            'tariffs.view'   => ['display_name' => 'Расчётные тарифы: просмотр', 'group' => 'Тарифы'],
            'tariff_groups.view' => ['display_name' => 'Группы тарифов: просмотр', 'group' => 'Тарифы'],
            'client_tariffs.view' => ['display_name' => 'Тарифы для клиентов: просмотр', 'group' => 'Тарифы'],
            // ... остальные *create/update/delete/toggle

            // группа «Исполнители»
            'drivers.view'        => ['display_name' => 'Водители: просмотр', 'group' => 'Исполнители'],
            'driver_groups.view'  => ['display_name' => 'Группы исполнителей: просмотр', 'group' => 'Исполнители'],
            'vehicles.view'       => ['display_name' => 'Автомобили: просмотр', 'group' => 'Исполнители'],
            // ...

            // ACL
            'acl.manage' => ['display_name' => 'Управление доступом (ACL)', 'group' => 'Администрирование'],

            'clients.view'   => ['display_name' => 'Клиенты: просмотр',       'group' => 'Клиенты'],
            'clients.create' => ['display_name' => 'Клиенты: создание',       'group' => 'Клиенты'],
            'clients.update' => ['display_name' => 'Клиенты: редактирование', 'group' => 'Клиенты'],
            'clients.delete' => ['display_name' => 'Клиенты: удаление',       'group' => 'Клиенты'],
            'clients.toggle' => ['display_name' => 'Клиенты: чёрный список',  'group' => 'Клиенты'],

            // Организации
            'organizations.view'      => ['display_name' => 'Организации: просмотр',              'group' => 'Организации'],
            'organizations.create'    => ['display_name' => 'Организации: создание',              'group' => 'Организации'],
            'organizations.update'    => ['display_name' => 'Организации: редактирование',        'group' => 'Организации'],
            'organizations.delete'    => ['display_name' => 'Организации: удаление',              'group' => 'Организации'],
            'organizations.toggle'    => ['display_name' => 'Организации: вкл/выкл',              'group' => 'Организации'],
            'organizations.balance'   => ['display_name' => 'Организации: операции по балансу',   'group' => 'Организации'],
            'organizations.employees' => ['display_name' => 'Организации: сотрудники',            'group' => 'Организации'],

            'client_economy.view'  => ['display_name' => 'Клиентская экономика: просмотр',   'group' => 'Клиенты'],
            'client_economy.topup' => ['display_name' => 'Клиентская экономика: пополнение', 'group' => 'Клиенты'],
            'client_economy.debit' => ['display_name' => 'Клиентская экономика: списание',   'group' => 'Клиенты'],

            'org_economy.view'  => ['display_name' => 'Экономика организаций: просмотр',   'group' => 'Организации'],
            'org_economy.topup' => ['display_name' => 'Экономика организаций: пополнение', 'group' => 'Организации'],
            'org_economy.debit' => ['display_name' => 'Экономика организаций: списание',   'group' => 'Организации'],

            'promocodes.view'   => ['display_name' => 'Промокоды: просмотр',   'group' => 'Лояльность'],
            'promocodes.create' => ['display_name' => 'Промокоды: создание',   'group' => 'Лояльность'],
            'promocodes.update' => ['display_name' => 'Промокоды: редактирование', 'group' => 'Лояльность'],
            'promocodes.delete' => ['display_name' => 'Промокоды: удаление',   'group' => 'Лояльность'],
            'promocodes.apply'  => ['display_name' => 'Промокоды: применить',  'group' => 'Лояльность'],
            'referrals.view'    => ['display_name' => 'Рефералы: просмотр',    'group' => 'Лояльность'],
            'referrals.manage'  => ['display_name' => 'Рефералы: управление',  'group' => 'Лояльность'],

            'orders.view'        => ['display_name' => 'Заказы: просмотр', 'group' => 'Заказы'],
            'orders.create'      => ['display_name' => 'Заказы: создание', 'group' => 'Заказы'],
            'orders.update'      => ['display_name' => 'Заказы: редактирование', 'group' => 'Заказы'],
            'orders.delete'      => ['display_name' => 'Заказы: удаление', 'group' => 'Заказы'],
            'orders.assign'      => ['display_name' => 'Заказы: назначение', 'group' => 'Заказы'],
            'orders.search'      => ['display_name' => 'Заказы: авто-распределение', 'group' => 'Заказы'],
            'orders.cancel'      => ['display_name' => 'Заказы: отмена', 'group' => 'Заказы'],
            'orders.complete'    => ['display_name' => 'Заказы: завершение', 'group' => 'Заказы'],
            'orders.finance'     => ['display_name' => 'Заказы: финансы', 'group' => 'Заказы'],
            'orders.attachments' => ['display_name' => 'Заказы: вложения', 'group' => 'Заказы'],
            'orders.map'         => ['display_name' => 'Заказы: карта', 'group' => 'Заказы'],

        ];

        // после цикла создания $perms:
        foreach ($permMeta as $name => $meta) {
            $p = Permission::where('name', $name)->first();
            if ($p) {
                $p->fill($meta)->save();
            }
        }

        $all = collect($perms);

        $expand = function (array $items) use ($all) {
            return collect($items)->flatMap(function ($pat) use ($all) {
                if ($pat === '*') return $all;
                if (str_contains($pat, '*')) {
                    $prefix = rtrim($pat, '*');
                    return $all->filter(fn($x) => str_starts_with($x, $prefix));
                }
                return [$pat];
            })->unique()->values()->all();
        };

        foreach ($map as $roleName => $allowed) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guard]);
            $role->syncPermissions($expand($allowed));
        }
    }
}
