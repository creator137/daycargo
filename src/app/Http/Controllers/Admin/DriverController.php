<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DriverRequest;
use App\Models\Driver;
use App\Models\DriverFile;
use App\Models\DriverGroup;
use App\Models\VehicleType;
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

        // docs отдельно (в drivers нет такого поля)
        $docs = $data['docs'] ?? null;
        unset($data['docs']);

        if ($req->hasFile('avatar')) {
            $data['avatar_path'] = $req->file('avatar')->store('drivers', 'public');
        }

        $data['updated_by'] = Auth::id();

        $driver = Driver::create($data);

        $this->saveDriverDocs($driver, $req, $docs);

        return redirect()->route('admin.drivers.index')->with('success', 'Водитель создан.');
    }

    public function edit(Driver $driver)
    {
        $vehicleTypes  = VehicleType::orderBy('capacity_kg')->pluck('name', 'id');
        $driverGroups  = DriverGroup::orderBy('name')->pluck('name', 'id');
        $citiesOptions = $this->citiesOptions();

        // чтобы форма могла показать "Открыть" по docs
        $driver->load('files');

        return view('admin.drivers.form', compact('driver', 'vehicleTypes', 'driverGroups', 'citiesOptions'));
    }

    public function update(DriverRequest $req, Driver $driver)
    {
        $data = $req->validated();

        if (!empty($data['password'])) {
            $data['app_password'] = Hash::make($data['password']);
        }
        unset($data['password'], $data['password_confirmation']);

        $docs = $data['docs'] ?? null;
        unset($data['docs']);

        if ($req->hasFile('avatar')) {
            $newPath = $req->file('avatar')->store('drivers', 'public');
            if ($driver->avatar_path) {
                Storage::disk('public')->delete($driver->avatar_path);
            }
            $data['avatar_path'] = $newPath;
        }

        $data['updated_by'] = Auth::id();

        $driver->update($data);

        $this->saveDriverDocs($driver, $req, $docs);

        return redirect()->route('admin.drivers.index')->with('success', 'Сохранено.');
    }

    public function destroy(Driver $driver)
    {
        if ($driver->avatar_path) {
            Storage::disk('public')->delete($driver->avatar_path);
        }

        // удалить физические файлы документов
        $driver->load('files');
        foreach ($driver->files as $f) {
            if (!empty($f->path)) {
                Storage::disk('public')->delete($f->path);
            }
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

    private function saveDriverDocs(Driver $driver, Request $req, ?array $docs): void
    {
        if (!$docs || !is_array($docs)) {
            return;
        }

        foreach ($docs as $type => $file) {
            if (!$req->hasFile("docs.$type")) {
                continue;
            }

            $uploaded = $req->file("docs.$type");
            if (!$uploaded || !$uploaded->isValid()) {
                continue;
            }

            // удалить старое по типу (и файл, и запись)
            $old = $driver->files()->where('type', $type)->get();
            foreach ($old as $o) {
                if (!empty($o->path)) {
                    Storage::disk('public')->delete($o->path);
                }
                $o->delete();
            }

            $path = $uploaded->store("drivers/docs/{$driver->id}", 'public');

            DriverFile::create([
                'driver_id'     => $driver->id,
                'type'          => (string) $type,
                'path'          => $path,
                'original_name' => $uploaded->getClientOriginalName(),
                'size'          => $uploaded->getSize(),
                'mime'          => $uploaded->getMimeType(),
            ]);
        }
    }
}
