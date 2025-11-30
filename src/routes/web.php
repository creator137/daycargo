<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\VehicleTypeController;
use App\Http\Controllers\Admin\TariffController;
use App\Http\Controllers\Admin\CancelReasonController;
use App\Http\Controllers\Admin\TariffGroupController;
use App\Http\Controllers\Admin\ClientTariffController;
use App\Http\Controllers\Admin\DriverGroupController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\Acl\RoleController;
use App\Http\Controllers\Admin\Acl\PermissionController;
use App\Http\Controllers\Admin\Acl\UserRoleController;
use App\Http\Controllers\Admin\Acl\MatrixController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\ReportController;

Route::get('/', function () {
    if (Auth::check()) {
        // если уже залогинен – сразу в админку
        return redirect()->route('admin.dashboard');
    }

    // если гость – на страницу логина (Breeze)
    return redirect()->route('login');
});

// TEST: deployed from local machine
// Профиль (Breeze стандарт)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['web', 'auth', 'verified', 'role:owner|admin|accountant|viewer'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::get('/reports/summary', [ReportController::class, 'summary'])
            ->name('reports.summary');

        // эндпоинт для AJAX-графиков:
        Route::get('/reports/summary/data', [ReportController::class, 'summaryData'])
            ->name('reports.summary.data');

        // «Главная» (если нужно туда же сводку)
        Route::get('/', [ReportController::class, 'dashboard'])->name('dashboard');
    });

Route::prefix('admin/acl')
    ->name('admin.acl.')
    ->middleware(['web', 'auth', 'verified', 'can:acl.manage'])
    ->group(function () {
        // Роли
        Route::get('/roles',              [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create',       [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles',             [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit',  [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}',       [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}',    [RoleController::class, 'destroy'])->name('roles.destroy');

        // Права
        Route::get('/permissions',                 [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('/permissions/create',          [PermissionController::class, 'create'])->name('permissions.create');
        Route::post('/permissions',                [PermissionController::class, 'store'])->name('permissions.store');
        Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
        Route::put('/permissions/{permission}',    [PermissionController::class, 'update'])->name('permissions.update');
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

        // Пользователи → роли
        Route::get('/users',              [UserRoleController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit',  [UserRoleController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}',       [UserRoleController::class, 'update'])->name('users.update');

        // Матрица ролей и прав
        Route::get('/matrix',  [MatrixController::class, 'index'])->name('matrix.index');
        Route::post('/matrix', [MatrixController::class, 'update'])->name('matrix.update');
    });

// Админка (для ролей owner|admin|accountant|viewer)
Route::middleware(['auth', 'verified', 'role:owner|admin|accountant|viewer'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::view('/', 'admin.dashboard')->name('dashboard');

        // Заказы
        Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class)
            ->except(['show'])
            ->parameters(['orders' => 'order'])
            ->names('orders');

        // Клиенты
        Route::resource('clients', ClientController::class)
            ->except(['show'])
            ->parameters(['clients' => 'client'])
            ->names('clients');

        Route::patch('clients/{client}/toggle-blacklist', [ClientController::class, 'toggleBlacklist'])
            ->name('clients.toggleBlacklist');

        Route::name('organizations.')
            ->prefix('clients/organizations')
            ->group(function () {
                Route::get('/', [OrganizationController::class, 'index'])->name('index');
                Route::get('/create', [OrganizationController::class, 'create'])->name('create');
                Route::post('/', [OrganizationController::class, 'store'])->name('store');
                Route::get('/{organization}/edit', [OrganizationController::class, 'edit'])->name('edit');
                Route::put('/{organization}', [OrganizationController::class, 'update'])->name('update');
                Route::delete('/{organization}', [OrganizationController::class, 'destroy'])->name('destroy');

                Route::patch('/{organization}/toggle', [OrganizationController::class, 'toggle'])->name('toggle');
                Route::post('/{organization}/topup', [OrganizationController::class, 'topup'])->name('topup');

                Route::get('/{organization}/employees', [OrganizationController::class, 'employees'])->name('employees');
                Route::post('/{organization}/employees', [OrganizationController::class, 'employeesAttach'])->name('employees.attach');
                Route::delete('/{organization}/employees/{client}', [OrganizationController::class, 'employeesDetach'])->name('employees.detach');
            });


        // Вкладка «Бонусная система» в разделе Клиенты (отдельная страница)
        Route::get('/loyalty', [\App\Http\Controllers\Admin\LoyaltyController::class, 'index'])
            ->name('loyalty.index')
            ->middleware(['can:promocodes.view']);

        // Промокоды (ресурс)
        Route::prefix('loyalty/promocodes')->name('loyalty.promocodes.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\PromoCodeController::class, 'index'])->name('index')->middleware('can:promocodes.view');
            Route::get('/create', [\App\Http\Controllers\Admin\PromoCodeController::class, 'create'])->name('create')->middleware('can:promocodes.create');
            Route::post('/', [\App\Http\Controllers\Admin\PromoCodeController::class, 'store'])->name('store')->middleware('can:promocodes.create');
            Route::get('/{promo_code}/edit', [\App\Http\Controllers\Admin\PromoCodeController::class, 'edit'])->name('edit')->middleware('can:promocodes.update');
            Route::put('/{promo_code}', [\App\Http\Controllers\Admin\PromoCodeController::class, 'update'])->name('update')->middleware('can:promocodes.update');
            Route::delete('/{promo_code}', [\App\Http\Controllers\Admin\PromoCodeController::class, 'destroy'])->name('destroy')->middleware('can:promocodes.delete');
            Route::patch('/{promo_code}/toggle', [\App\Http\Controllers\Admin\PromoCodeController::class, 'toggle'])->name('toggle')->middleware('can:promocodes.update');
        });

        // Применить промокод к клиенту (из карточки клиента)
        Route::post('clients/{client}/promo/apply', [\App\Http\Controllers\Admin\ClientController::class, 'promoApply'])
            ->name('clients.promo.apply')
            ->middleware('can:promocodes.apply');

        // Клиентская экономика (баланс/бонусы)
        Route::post('clients/{client}/wallet', [ClientController::class, 'walletOperation'])
            ->name('clients.wallet')        // expects ClientWalletOperationRequest
            ->middleware('can:client_economy.view');

        Route::post('clients/{client}/bonus/grant', [ClientController::class, 'bonusGrant'])
            ->name('clients.bonus.grant')   // optional helper (заполняет bonus_entries и wallet_transactions бонусов)
            ->middleware('can:client_economy.topup');

        // Алиас для пункта меню «Организации»
        Route::get('/clients/orgs', fn() => redirect()->route('admin.organizations.index'))
            ->name('clients.orgs');

        // Исполнители
        Route::view('/vehicles', 'admin.vehicles.index')->name('vehicles.index');
        Route::view('/driver-tariffs', 'admin.drivers.tariffs')->name('drivers.tariffs');
        Route::view('/photo-control', 'admin.drivers.photo')->name('drivers.photo');
        Route::view('/drivers/import', 'admin.drivers.import')->name('drivers.import');
        Route::view('/drivers/bonuses', 'admin.drivers.bonuses')->name('drivers.bonuses');

        // Автомобили
        Route::resource('vehicles', \App\Http\Controllers\Admin\VehicleController::class)
            ->except(['show'])
            ->parameters(['vehicles' => 'vehicle'])
            ->names('vehicles');

        Route::patch('vehicles/{vehicle}/toggle', [\App\Http\Controllers\Admin\VehicleController::class, 'toggle'])
            ->name('vehicles.toggle');

        // Тарифы
        Route::resource('tariffs', TariffController::class)
            ->except(['show'])
            ->parameters(['tariffs' => 'tariff'])
            ->names('tariffs');

        Route::patch('tariffs/{tariff}/toggle', [TariffController::class, 'toggle'])
            ->name('tariffs.toggle');



        // Интеграции (внешние компании: ПЭК, «Петрович» и т.п.)
        Route::view('/integrations', 'admin.integrations.index')->name('integrations.index');


        // Справочники
        Route::view('/dicts', 'admin.dicts.index')->name('dicts.index');

        //Справочники - Типы авто — короткий resource + toggle
        Route::resource('dicts/vehicle-types', VehicleTypeController::class)
            ->except(['show'])
            ->parameters(['vehicle-types' => 'vehicleType'])
            ->names([
                'index'   => 'dicts.vehicle_types',   // сохранить старое имя для индекса
                'create'  => 'vehicle_types.create',
                'store'   => 'vehicle_types.store',
                'edit'    => 'vehicle_types.edit',
                'update'  => 'vehicle_types.update',
                'destroy' => 'vehicle_types.destroy',
            ]);

        Route::patch('dicts/vehicle-types/{vehicleType}/toggle', [VehicleTypeController::class, 'toggle'])
            ->name('vehicle_types.toggle');

        Route::view('/dicts/services', 'admin.dicts.services')->name('dicts.services');

        //Справочники - Причины отмены — resource + toggle (имя индекса сохраняем для меню)
        Route::resource('dicts/cancel-reasons', CancelReasonController::class)
            ->except(['show'])
            ->parameters(['cancel-reasons' => 'cancelReason'])
            ->names([
                'index'   => 'dicts.cancel_reasons',
                'create'  => 'cancel_reasons.create',
                'store'   => 'cancel_reasons.store',
                'edit'    => 'cancel_reasons.edit',
                'update'  => 'cancel_reasons.update',
                'destroy' => 'cancel_reasons.destroy',
            ]);

        Route::patch(
            'dicts/cancel-reasons/{cancelReason}/toggle',
            [\App\Http\Controllers\Admin\CancelReasonController::class, 'toggle']
        )->name('cancel_reasons.toggle');

        //Справочники - группы тарифов
        Route::resource('dicts/tariff-groups', TariffGroupController::class)
            ->except(['show'])
            ->parameters(['tariff-groups' => 'tariffGroup'])
            ->names([
                'index'   => 'dicts.tariff_groups',
                'create'  => 'tariff_groups.create',
                'store'   => 'tariff_groups.store',
                'edit'    => 'tariff_groups.edit',
                'update'  => 'tariff_groups.update',
                'destroy' => 'tariff_groups.destroy',
            ]);

        Route::patch('dicts/tariff-groups/{tariffGroup}/toggle', [TariffGroupController::class, 'toggle'])
            ->name('tariff_groups.toggle');

        // Справочник «Города»
        Route::resource('dicts/cities', CityController::class)
            ->except(['show'])
            ->parameters(['cities' => 'city'])
            ->names([
                'index'   => 'dicts.cities',
                'create'  => 'cities.create',
                'store'   => 'cities.store',
                'edit'    => 'cities.edit',
                'update'  => 'cities.update',
                'destroy' => 'cities.destroy',
            ]);

        Route::patch('dicts/cities/{city}/toggle', [CityController::class, 'toggle'])
            ->name('cities.toggle');
        // Тарифы клиентов
        Route::resource('client-tariffs', ClientTariffController::class)
            ->except(['show'])
            ->parameters(['client-tariffs' => 'clientTariff'])
            ->names('client_tariffs');

        Route::patch('client-tariffs/{clientTariff}/toggle', [ClientTariffController::class, 'toggle'])
            ->name('client_tariffs.toggle');

        // Группы вводитилей
        Route::resource('driver-groups', DriverGroupController::class)
            ->except(['show'])
            ->parameters(['driver-groups' => 'driverGroup'])
            ->names('driver_groups');

        Route::patch('driver-groups/{driverGroup}/toggle', [DriverGroupController::class, 'toggle'])
            ->name('driver_groups.toggle');

        // Настройки
        Route::view('/settings', 'admin.settings.index')->name('settings.index');
        Route::view('/settings/statuses', 'admin.settings.statuses')->name('settings.statuses');

        // Исполнители
        Route::resource('drivers', \App\Http\Controllers\Admin\DriverController::class)
            ->except(['show'])
            ->parameters(['drivers' => 'driver'])
            ->names('drivers');

        Route::patch('drivers/{driver}/toggle', [\App\Http\Controllers\Admin\DriverController::class, 'toggle'])
            ->name('drivers.toggle');
    });

require __DIR__ . '/auth.php';
