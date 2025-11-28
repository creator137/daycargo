<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DriverGroupRequest;
use App\Models\ClientTariff;
use App\Models\DriverGroup;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use App\Models\City;

class DriverGroupController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(DriverGroup::class, 'driverGroup');
    }

    public function index(Request $req)
    {
        $status = $req->string('status')->toString() ?: 'active';

        $q = DriverGroup::with('vehicleType')
            ->when($status === 'active',  fn($qq) => $qq->where('active', true))
            ->when($status === 'blocked', fn($qq) => $qq->where('active', false))
            ->when($req->filled('city'),       fn($qq) => $qq->where('city', $req->string('city')))
            ->when($req->filled('profession'), fn($qq) => $qq->where('profession', 'like', '%' . $req->string('profession') . '%'))
            ->when($req->filled('name'),       fn($qq) => $qq->where('name', 'like', '%' . $req->string('name') . '%'))
            ->orderBy('sort')->orderBy('name');

        $items = $q->paginate(20)->withQueryString();

        // счётчики табов
        $counts = [
            'active'  => DriverGroup::where('active', true)->count(),
            'blocked' => DriverGroup::where('active', false)->count(),
        ];

        // опции для фильтров
        $cities = City::where('active', true)
            ->orderBy('sort')->orderBy('name')
            ->pluck('name', 'name')->toArray();

        return view('admin.drivers.groups.index', [
            'items'      => $items,
            'status'     => $status,
            'tabs'       => $counts,
            'cities'     => $cities,
        ]);
    }

    public function create()
    {
        $group = new DriverGroup([
            'priority' => 10,
            'sort'     => 100,
            'active'   => true,
            'visibility_mode' => 'own_and_lower',
            'visible_vehicle_type_ids' => [],
        ]);

        return view('admin.drivers.groups.form', [
            'group'             => $group,
            'vehicleTypes'      => VehicleType::orderBy('capacity_kg')->pluck('name', 'id'),
            'clientTariffs'     => ClientTariff::orderBy('sort')->pluck('name', 'id'),
            'selectedTariffIds' => [],
            'citiesOptions'     => City::where('active', true)
                ->orderBy('sort')->orderBy('name')
                ->pluck('name', 'name')->toArray(),
        ]);
    }

    public function store(DriverGroupRequest $request)
    {
        $data = $request->validated();

        $tariffIds = $data['client_tariff_ids'] ?? [];
        unset($data['client_tariff_ids']);

        $group = DriverGroup::create($data);
        $group->clientTariffs()->sync($tariffIds);

        return redirect()->route('admin.driver_groups.index')->with('success', 'Группа создана.');
    }

    public function show(DriverGroup $driverGroup)
    {
        abort(404);
    }

    public function edit(DriverGroup $driverGroup)
    {
        return view('admin.drivers.groups.form', [
            'group'             => $driverGroup,
            'vehicleTypes'      => VehicleType::orderBy('capacity_kg')->pluck('name', 'id'),
            'clientTariffs'     => ClientTariff::orderBy('sort')->pluck('name', 'id'),
            'selectedTariffIds' => $driverGroup->clientTariffs()->pluck('client_tariffs.id')->all(),
            'citiesOptions'     => City::where('active', true)
                ->orderBy('sort')->orderBy('name')
                ->pluck('name', 'name')->toArray(),
        ]);
    }

    public function update(DriverGroupRequest $request, DriverGroup $driverGroup)
    {
        $data = $request->validated();

        $tariffIds = $data['client_tariff_ids'] ?? [];
        unset($data['client_tariff_ids']);

        $driverGroup->update($data);
        $driverGroup->clientTariffs()->sync($tariffIds);

        return redirect()->route('admin.driver_groups.index')->with('success', 'Сохранено.');
    }

    public function destroy(DriverGroup $driverGroup)
    {
        $driverGroup->delete();
        return redirect()->route('admin.driver_groups.index')->with('success', 'Удалено.');
    }

    public function toggle(DriverGroup $driverGroup)
    {
        $this->authorize('toggle', $driverGroup);
        $driverGroup->active = ! $driverGroup->active;
        $driverGroup->save();

        return back()->with('success', 'Статус изменён.');
    }
}
