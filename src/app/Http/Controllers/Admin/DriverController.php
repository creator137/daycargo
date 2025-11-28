<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DriverRequest;
use App\Models\Driver;
use App\Models\DriverGroup;
use App\Models\VehicleType;
use App\Models\Tariff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\City;

class DriverController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Driver::class, 'driver');
    }

    private function citiesOptions(): array
    {
        return City::where('active', true)
            ->orderBy('sort')->orderBy('name')
            ->pluck('name', 'name')
            ->toArray();
    }

    public function index(Request $req)
    {
        $status = $req->string('status')->toString() ?: 'active';

        $q = Driver::with(['vehicleType', 'driverGroup', 'updatedBy'])
            ->when($status, fn($qq) => $qq->where('status', $status))
            ->when($req->filled('city'), function ($qq) use ($req) {
                $city = $req->string('city')->toString();
                $qq->where(function ($w) use ($city) {
                    $w->where('main_city', $city)
                        ->orWhereJsonContains('cities', $city);
                });
            })
            ->when($req->filled('vehicle_type_id'), fn($qq) => $qq->where('vehicle_type_id', $req->integer('vehicle_type_id')))
            ->when($req->filled('driver_group_id'), fn($qq) => $qq->where('driver_group_id', $req->integer('driver_group_id')))
            ->when($req->filled('partner'), fn($qq) => $qq->where('partner_name', 'like', '%' . $req->string('partner') . '%'))
            ->when($req->filled('search'), function ($qq) use ($req) {
                $s = '%' . $req->string('search') . '%';
                $qq->where(function ($w) use ($s) {
                    $w->where('full_name', 'like', $s)
                        ->orWhere('phone', 'like', $s)
                        ->orWhere('email', 'like', $s)
                        ->orWhere('callsign', 'like', $s);
                });
            })
            ->orderByDesc('created_at');

        $items = $q->paginate(20)->withQueryString();

        $counts = Driver::selectRaw('status, COUNT(*) as cnt')->groupBy('status')->pluck('cnt', 'status');
        $tabs = [
            'active'  => $counts['active']  ?? 0,
            'blocked' => $counts['blocked'] ?? 0,
            'pending' => $counts['pending'] ?? 0,
        ];

        $vehicleTypes  = VehicleType::orderBy('capacity_kg')->pluck('name', 'id');
        $driverGroups  = DriverGroup::orderBy('name')->pluck('name', 'id');
        $citiesOptions = $this->citiesOptions();

        return view('admin.drivers.index', compact('items', 'tabs', 'status', 'vehicleTypes', 'driverGroups', 'citiesOptions'));
    }

    public function create()
    {
        $driver = new Driver([
            'status'            => 'pending',
            'supports_terminal' => false,
            'sort'              => 100,
        ]);

        $vehicleTypes  = VehicleType::orderBy('capacity_kg')->pluck('name', 'id');
        $driverGroups  = DriverGroup::orderBy('name')->pluck('name', 'id');
        $citiesOptions = $this->citiesOptions();

        return view('admin.drivers.form', compact('driver', 'vehicleTypes', 'driverGroups', 'citiesOptions'));
    }

    public function store(DriverRequest $req)
    {
        $data = $req->validated();

        if (!empty($data['password'])) {
            $data['app_password'] = Hash::make($data['password']);
        }
        unset($data['password'], $data['password_confirmation']);

        if ($req->hasFile('avatar')) {
            $data['avatar_path'] = $req->file('avatar')->store('drivers', 'public');
        }

        $data['updated_by'] = Auth::id();

        Driver::create($data);

        return redirect()->route('admin.drivers.index')->with('success', 'Водитель создан.');
    }

    public function edit(Driver $driver)
    {
        $vehicleTypes  = VehicleType::orderBy('capacity_kg')->pluck('name', 'id');
        $driverGroups  = DriverGroup::orderBy('name')->pluck('name', 'id');
        $citiesOptions = $this->citiesOptions();

        return view('admin.drivers.form', compact('driver', 'vehicleTypes', 'driverGroups', 'citiesOptions'));
    }

    public function update(DriverRequest $req, Driver $driver)
    {
        $data = $req->validated();

        if (!empty($data['password'])) {
            $data['app_password'] = Hash::make($data['password']);
        }
        unset($data['password'], $data['password_confirmation']);

        if ($req->hasFile('avatar')) {
            $newPath = $req->file('avatar')->store('drivers', 'public');
            if ($driver->avatar_path) {
                Storage::disk('public')->delete($driver->avatar_path);
            }
            $data['avatar_path'] = $newPath;
        }

        $data['updated_by'] = Auth::id();

        $driver->update($data);

        return redirect()->route('admin.drivers.index')->with('success', 'Сохранено.');
    }

    public function destroy(Driver $driver)
    {
        if ($driver->avatar_path) {
            Storage::disk('public')->delete($driver->avatar_path);
        }
        $driver->delete();
        return back()->with('success', 'Удалено.');
    }

    public function toggle(Driver $driver)
    {
        $this->authorize('toggle', $driver);

        // pending/blocked -> active, active -> blocked
        $driver->status = $driver->status === 'active' ? 'blocked' : 'active';
        $driver->updated_by = Auth::id();
        $driver->save();

        return back()->with('success', 'Статус изменён.');
    }
}
