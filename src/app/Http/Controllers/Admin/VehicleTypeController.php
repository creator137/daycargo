<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleTypeRequest;
use App\Models\VehicleType;

class VehicleTypeController extends Controller
{
    public function __construct()
    {
        // index->viewAny, create/store->create, edit/update->update, destroy->delete
        $this->authorizeResource(VehicleType::class, 'vehicleType');
    }

    /** Листинг */
    public function index()
    {
        $items = VehicleType::orderBy('capacity_kg')->get();
        return view('admin.dicts.vehicle.index', compact('items'));
    }

    /** Форма создания */
    public function create()
    {
        $item = new VehicleType(['active' => true]);
        return view('admin.dicts.vehicle.form', compact('item'));
    }

    /** Создание */
    public function store(VehicleTypeRequest $request)
    {
        VehicleType::create($request->validated());

        return redirect()
            ->route('admin.dicts.vehicle_types') // индекс оставляем со старым именем
            ->with('success', 'Тип авто создан.');
    }

    /** Форма редактирования */
    public function edit(VehicleType $vehicleType)
    {
        return view('admin.dicts.vehicle.form', ['item' => $vehicleType]);
    }

    /** Обновление */
    public function update(VehicleTypeRequest $request, VehicleType $vehicleType)
    {
        $vehicleType->update($request->validated());

        return redirect()
            ->route('admin.dicts.vehicle_types')
            ->with('success', 'Тип авто обновлён.');
    }

    /** Удаление */
    public function destroy(VehicleType $vehicleType)
    {
        $vehicleType->delete();

        return redirect()
            ->route('admin.dicts.vehicle_types')
            ->with('success', 'Тип авто удалён.');
    }

    /** Быстрое вкл/выкл */
    public function toggle(VehicleType $vehicleType)
    {
        $this->authorize('toggle', $vehicleType);

        $vehicleType->active = ! $vehicleType->active;
        $vehicleType->save();

        return back()->with('success', 'Статус изменён.');
    }
}
