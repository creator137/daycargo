<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientTariffRequest;
use App\Models\ClientTariff;
use App\Models\TariffGroup;
use App\Models\VehicleType;
use App\Models\City;

class ClientTariffController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ClientTariff::class, 'clientTariff');
    }

    public function index()
    {
        $items = ClientTariff::with(['group', 'vehicleType'])
            ->orderBy('sort')->orderBy('name')->get();

        return view('admin.client_tariffs.index', compact('items'));
    }

    public function create()
    {
        $tariff = new ClientTariff([
            'available_site' => true,
            'available_app'  => true,
            'available_dispatcher' => true,
            'available_driver' => false,
            'available_cabinet' => true,
            'addresses_min' => 1,
            'sort' => 100,
            'active' => true,
        ]);

        return view('admin.client_tariffs.form', [
            'tariff'        => $tariff,
            'groups'        => TariffGroup::orderBy('sort')->pluck('name', 'id'),              // можно оставить коллекцией
            'vehicleTypes'  => VehicleType::orderBy('capacity_kg')->pluck('name', 'id'),       // можно оставить коллекцией
            'citiesOptions' => City::where('active', true)
                ->orderBy('sort')->orderBy('name')
                ->pluck('name', 'name')
                ->toArray(), // ← ВАЖНО
        ]);
    }

    public function store(ClientTariffRequest $request)
    {
        ClientTariff::create($request->validated());
        return redirect()->route('admin.client_tariffs.index')->with('success', 'Тариф создан.');
    }

    public function show(ClientTariff $clientTariff)
    {
        abort(404);
    }

    public function edit(ClientTariff $clientTariff)
    {
        return view('admin.client_tariffs.form', [
            'tariff'        => $clientTariff,
            'groups'        => TariffGroup::orderBy('sort')->pluck('name', 'id'),
            'vehicleTypes'  => VehicleType::orderBy('capacity_kg')->pluck('name', 'id'),
            'citiesOptions' => City::where('active', true)
                ->orderBy('sort')->orderBy('name')
                ->pluck('name', 'name')
                ->toArray(), // ← ВАЖНО
        ]);
    }

    public function update(ClientTariffRequest $request, ClientTariff $clientTariff)
    {
        $clientTariff->update($request->validated());
        return redirect()->route('admin.client_tariffs.index')->with('success', 'Тариф сохранён.');
    }

    public function destroy(ClientTariff $clientTariff)
    {
        $clientTariff->delete();
        return redirect()->route('admin.client_tariffs.index')->with('success', 'Удалено.');
    }

    public function toggle(ClientTariff $clientTariff)
    {
        $this->authorize('toggle', $clientTariff);
        $clientTariff->active = ! $clientTariff->active;
        $clientTariff->save();

        return back()->with('success', 'Статус изменён.');
    }
}
