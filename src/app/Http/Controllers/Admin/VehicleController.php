<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleRequest;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\Driver;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Vehicle::class, 'vehicle');
    }

    private function cityOptions(): array
    {
        return City::where('active', true)
            ->orderBy('sort')
            ->orderBy('name')
            ->pluck('name', 'name')
            ->toArray();
    }

    /** Стабильно собираем подписи водителей в PHP, без БД-функций */
    private function driverOptions(): \Illuminate\Support\Collection
    {
        return Driver::query()
            ->select(['id', 'full_name', 'callsign', 'phone'])
            ->orderByRaw("
                CASE WHEN full_name IS NULL OR full_name = '' THEN 1 ELSE 0 END,
                full_name ASC, callsign ASC, phone ASC
            ")
            ->get()
            ->mapWithKeys(function (Driver $d) {
                $label = $d->full_name ?: ($d->callsign ?: $d->phone);
                if ($d->callsign) {
                    $label .= ' — ' . $d->callsign;
                }
                return [$d->id => $label];
            });
    }

    public function index(Request $req)
    {
        $status = $req->string('status')->toString() ?: 'active';

        $q = Vehicle::with(['driver', 'vehicleType'])
            ->when($status === 'active',  fn($qq) => $qq->where('status', 'active'))
            ->when($status === 'blocked', fn($qq) => $qq->where('status', 'blocked'))
            ->when($status === 'pending', fn($qq) => $qq->where('status', 'pending'))
            ->when($req->filled('city'), fn($qq) => $qq->where('city', $req->string('city')))
            ->when($req->filled('vehicle_type_id'), fn($qq) => $qq->where('vehicle_type_id', $req->integer('vehicle_type_id')))
            ->when($req->filled('owner_type'), fn($qq) => $qq->where('owner_type', $req->string('owner_type')));

        if ($req->has('is_rent') && $req->input('is_rent') !== '') {
            $q->where('is_rent', (bool) $req->input('is_rent'));
        }

        if ($search = trim((string) $req->input('search'))) {
            $like = "%{$search}%";
            $q->where(function ($w) use ($like) {
                $w->where('brand', 'like', $like)
                    ->orWhere('model', 'like', $like)
                    ->orWhere('license_plate', 'like', $like)
                    ->orWhereHas('driver', function ($d) use ($like) {
                        $d->where('phone', 'like', $like)
                            ->orWhere('callsign', 'like', $like)
                            ->orWhere('full_name', 'like', $like);
                    });
            });
        }

        $items = $q->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $tabs = [
            'active'  => Vehicle::where('status', 'active')->count(),
            'blocked' => Vehicle::where('status', 'blocked')->count(),
            'pending' => Vehicle::where('status', 'pending')->count(),
        ];

        return view('admin.vehicles.index', [
            'items'        => $items,
            'tabs'         => $tabs,
            'status'       => $status,
            'cities'       => $this->cityOptions(),
            'vehicleTypes' => VehicleType::orderBy('capacity_kg')->pluck('name', 'id'),
        ]);
    }

    public function create()
    {
        $vehicle = new Vehicle([
            'status'     => 'pending',
            'owner_type' => 'private',
            'is_rent'    => false,
            'city'       => null,
        ]);

        return view('admin.vehicles.form', [
            'vehicle'      => $vehicle,
            'cities'       => $this->cityOptions(),
            'vehicleTypes' => VehicleType::orderBy('capacity_kg')->pluck('name', 'id'),
            'drivers'      => $this->driverOptions(),
        ]);
    }

    public function store(VehicleRequest $request)
    {
        $data = $request->validated();

        // Загрузка фото
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('vehicles', 'public');
        }

        Vehicle::create($data);

        return redirect()->route('admin.vehicles.index')->with('success', 'Автомобиль создан.');
    }

    public function edit(Vehicle $vehicle)
    {
        return view('admin.vehicles.form', [
            'vehicle'      => $vehicle,
            'cities'       => $this->cityOptions(),
            'vehicleTypes' => VehicleType::orderBy('capacity_kg')->pluck('name', 'id'),
            'drivers'      => $this->driverOptions(),
        ]);
    }

    public function update(VehicleRequest $request, Vehicle $vehicle)
    {
        $data = $request->validated();

        // Новое фото (старое удаляем по желанию)
        if ($request->hasFile('photo')) {
            if ($vehicle->photo_path && Storage::disk('public')->exists($vehicle->photo_path)) {
                Storage::disk('public')->delete($vehicle->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('vehicles', 'public');
        }

        $vehicle->update($data);

        return redirect()->route('admin.vehicles.index')->with('success', 'Сохранено.');
    }

    public function destroy(Vehicle $vehicle)
    {
        // опционально удалить и фото
        if ($vehicle->photo_path && Storage::disk('public')->exists($vehicle->photo_path)) {
            Storage::disk('public')->delete($vehicle->photo_path);
        }

        $vehicle->delete();
        return back()->with('success', 'Удалено.');
    }

    public function toggle(Vehicle $vehicle)
    {
        $this->authorize('toggle', $vehicle);
        $vehicle->status = $vehicle->status === 'active' ? 'blocked' : 'active';
        $vehicle->save();

        return back()->with('success', 'Статус изменён.');
    }
}
