<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TariffRequest;
use App\Models\Tariff;
use App\Models\VehicleType;
use Illuminate\Http\Request;

class TariffController extends Controller
{
    public function __construct()
    {
        // Подключаем авторизацию для ресурсных методов:
        // index -> viewAny, create/store -> create, edit/update -> update, destroy -> delete
        $this->authorizeResource(\App\Models\Tariff::class, 'tariff');
    }

    /**
     * Список тарифов: сгруппированы по типу авто.
     */
    public function index()
    {
        $types = VehicleType::orderBy('capacity_kg')->get();

        $tariffs = Tariff::with('vehicleType')
            ->orderBy('vehicle_type_id')
            ->get()
            ->groupBy('vehicle_type_id');

        return view('admin.tariffs.index', compact('types', 'tariffs'));
    }

    /**
     * Форма создания. Предзаполняем vehicle_type, scope_type, active.
     * Ожидаем query-параметр ?vehicle_type=ID.
     */
    public function create(Request $request)
    {
        $vehicleTypeId = $request->integer('vehicle_type') ?: null;
        if ($vehicleTypeId && ! VehicleType::whereKey($vehicleTypeId)->exists()) {
            $vehicleTypeId = null; // защита от мусора в query
        }

        $tariff = new Tariff([
            'vehicle_type_id' => $vehicleTypeId,
            'scope_type'      => 'global',
            'active'          => true,
        ]);

        return view('admin.tariffs.form', [
            'tariff'       => $tariff,
            'vehicleTypes' => VehicleType::orderBy('capacity_kg')->pluck('name', 'id'),
            'scopeOptions' => Tariff::scopeTypeOptions(), // ['global' => 'Глобально', ...]
        ]);
    }

    /**
     * Создание тарифа.
     */
    public function store(TariffRequest $request)
    {
        Tariff::create($request->validated());

        return redirect()
            ->route('admin.tariffs.index')
            ->with('success', 'Тариф создан.');
    }

    /**
     * Форма редактирования.
     */
    public function edit(Tariff $tariff)
    {
        return view('admin.tariffs.form', [
            'tariff'       => $tariff,
            'vehicleTypes' => VehicleType::orderBy('capacity_kg')->pluck('name', 'id'),
            'scopeOptions' => Tariff::scopeTypeOptions(),
        ]);
    }

    /**
     * Обновление тарифа.
     */
    public function update(TariffRequest $request, Tariff $tariff)
    {
        $tariff->update($request->validated());

        return redirect()
            ->route('admin.tariffs.index')
            ->with('success', 'Тариф обновлён.');
    }

    /**
     * Удаление тарифа.
     */
    public function destroy(Tariff $tariff)
    {
        $tariff->delete();

        return redirect()
            ->route('admin.tariffs.index')
            ->with('success', 'Тариф удалён.');
    }

    /**
     * Быстрое переключение статуса active.
     */
    public function toggle(Tariff $tariff)
    {
        // Права на нестандартное действие
        $this->authorize('toggle', $tariff);

        $tariff->active = ! $tariff->active;
        $tariff->save();

        return back()->with('success', 'Статус изменён.');
    }
}
