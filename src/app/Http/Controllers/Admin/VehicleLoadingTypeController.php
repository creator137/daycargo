<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleLoadingType;
use Illuminate\Http\Request;

class VehicleLoadingTypeController extends Controller
{
    public function index()
    {
        $items = VehicleLoadingType::query()->orderBy('sort')->orderBy('name')->paginate(30);
        return view('admin.dicts.vehicle_loading_types.index', compact('items'));
    }

    public function create()
    {
        $item = new VehicleLoadingType(['active' => true, 'sort' => 100]);
        return view('admin.dicts.vehicle_loading_types.form', compact('item'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name'   => ['required', 'string', 'max:150'],
            'sort'   => ['nullable', 'integer', 'min:0'],
            'active' => ['sometimes', 'boolean'],
        ]);

        $data['sort'] = (int)($data['sort'] ?? 100);
        $data['active'] = (bool)($data['active'] ?? false);

        VehicleLoadingType::create($data);

        return redirect()->route('admin.dicts.vehicle_loading_types')->with('success', 'Создано.');
    }

    public function edit(VehicleLoadingType $vehicleLoadingType)
    {
        $item = $vehicleLoadingType;
        return view('admin.dicts.vehicle_loading_types.form', compact('item'));
    }

    public function update(Request $r, VehicleLoadingType $vehicleLoadingType)
    {
        $data = $r->validate([
            'name'   => ['required', 'string', 'max:150'],
            'sort'   => ['nullable', 'integer', 'min:0'],
            'active' => ['sometimes', 'boolean'],
        ]);

        $data['sort'] = (int)($data['sort'] ?? 100);
        $data['active'] = (bool)($data['active'] ?? false);

        $vehicleLoadingType->update($data);

        return back()->with('success', 'Сохранено.');
    }

    public function destroy(VehicleLoadingType $vehicleLoadingType)
    {
        $vehicleLoadingType->delete();
        return back()->with('success', 'Удалено.');
    }

    public function toggle(VehicleLoadingType $vehicleLoadingType)
    {
        $vehicleLoadingType->active = !$vehicleLoadingType->active;
        $vehicleLoadingType->save();

        return back()->with('success', 'Статус изменён.');
    }
}
