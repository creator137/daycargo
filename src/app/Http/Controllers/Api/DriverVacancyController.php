<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DriverVacancyController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            // personal
            'phone' => ['required', 'string', 'max:32'],
            'email' => ['required', 'email', 'max:255'],
            'name' => ['required', 'string', 'max:100'],
            'lastName' => ['required', 'string', 'max:100'],
            'secondName' => ['nullable', 'string', 'max:100'],
            'citizenship' => ['nullable', 'string', 'max:100'],

            // ✅ cityId = ID города
            'cityId' => ['nullable', 'integer', Rule::exists('cities', 'id')],

            'employment_type' => ['nullable', 'string', 'max:50'],

            // car
            'carBrand' => ['nullable', 'string', 'max:100'],
            'carModel' => ['nullable', 'string', 'max:100'],
            'carYear' => ['nullable', 'integer', 'min:1950', 'max:' . (date('Y') + 1)],
            'car' => ['nullable', 'boolean'],
            'carClass' => ['nullable', 'integer'],
            'carColor' => ['nullable', 'string', 'max:50'],
            'carGosNumber' => ['nullable', 'string', 'max:32'],

            // dimensions
            'additional.width' => ['nullable', 'numeric', 'min:0'],
            'additional.height' => ['nullable', 'numeric', 'min:0'],
            'additional.length' => ['nullable', 'numeric', 'min:0'],

            // checkboxes
            'description' => ['nullable', 'array'],
            'description.*' => ['string', 'max:50'],
            'typeLoading' => ['nullable', 'array'],
            'typeLoading.*' => ['string', 'max:50'],

            // files
            'photo' => ['nullable', 'file', 'max:20480'],
            'carPhoto' => ['nullable', 'file', 'max:20480'],
            'osagoScan1' => ['nullable', 'file', 'max:20480'],
            'passportScan1' => ['nullable', 'file', 'max:20480'],
            'passportScan2' => ['nullable', 'file', 'max:20480'],
            'driverLicenseScan1' => ['nullable', 'file', 'max:20480'],
            'driverLicenseScan2' => ['nullable', 'file', 'max:20480'],
            'ptsScan1' => ['nullable', 'file', 'max:20480'],
            'ptsScan2' => ['nullable', 'file', 'max:20480'],
        ]);

        return DB::transaction(function () use ($request, $data) {

            // cityId -> city_id + main_city (name)
            $cityIdInt = isset($data['cityId']) ? (int)$data['cityId'] : null;
            $cityName = null;

            if (!empty($cityIdInt)) {
                $city = City::query()->select(['id', 'name'])->find($cityIdInt);
                $cityName = $city?->name;
            }

            $firstName  = $data['name'];
            $lastName   = $data['lastName'];
            $secondName = $data['secondName'] ?? null;
            $fullName   = trim($lastName . ' ' . $firstName . ' ' . ($secondName ?? ''));

            $driver = Driver::create([
                'status' => 'pending',

                'phone' => $data['phone'],
                'email' => $data['email'],

                'first_name' => $firstName,
                'last_name' => $lastName,
                'second_name' => $secondName,
                'full_name' => $fullName,

                'citizenship' => $data['citizenship'] ?? null,
                'employment_type' => $data['employment_type'] ?? null,

                // ✅ привязка к справочнику
                'city_id' => $cityIdInt,
                'main_city' => $cityName,

                'supports_terminal' => false,
                'sort' => 100,

                'app_password' => Hash::make(bin2hex(random_bytes(8))),
            ]);

            // фото водителя -> avatar_path
            if ($request->hasFile('photo')) {
                $driver->avatar_path = $request->file('photo')->store("drivers/{$driver->id}", 'public');
                $driver->save();
            }

            $dimensions = null;
            if (!empty($data['additional'])) {
                $dimensions = [
                    'width' => $data['additional']['width'] ?? null,
                    'height' => $data['additional']['height'] ?? null,
                    'length' => $data['additional']['length'] ?? null,
                ];
            }

            $options = [];
            if (!empty($data['description'])) {
                $options['body_types'] = $data['description'];
            }
            if (!empty($data['typeLoading'])) {
                $options['loading_types'] = $data['typeLoading'];
            }

            // ✅ FIX: если в БД brand NOT NULL — подставляем дефолт, чтобы API не падало
            $brand = $data['carBrand'] ?? null;
            if ($brand === null || $brand === '') {
                $brand = '—'; // дефолт (можно 'Unknown')
            }

            $vehicle = Vehicle::create([
                'driver_id' => $driver->id,
                'status' => 'pending',

                'city' => $cityName,

                'owner_type' => 'private',
                'is_rent' => false,

                'vehicle_type_id' => null,

                'brand' => $brand,
                'model' => $data['carModel'] ?? null,
                'year' => $data['carYear'] ?? null,
                'color' => $data['carColor'] ?? null,
                'license_plate' => $data['carGosNumber'] ?? null,

                'not_in_list' => (bool)($data['car'] ?? false),
                'external_car_class_id' => $data['carClass'] ?? null,
                'dimensions' => $dimensions,

                'options' => $options ?: null,
            ]);

            // фото авто -> photo_path
            if ($request->hasFile('carPhoto')) {
                $vehicle->photo_path = $request->file('carPhoto')->store("vehicles/{$vehicle->id}", 'public');
                $vehicle->save();
            }

            // документы -> driver_files
            $docFields = [
                'osagoScan1',
                'passportScan1',
                'passportScan2',
                'driverLicenseScan1',
                'driverLicenseScan2',
                'ptsScan1',
                'ptsScan2',
            ];

            foreach ($docFields as $field) {
                if (!$request->hasFile($field)) continue;

                $file = $request->file($field);
                $path = $file->store("driver_docs/{$driver->id}", 'public');

                $driver->files()->create([
                    'type' => $field,
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                ]);
            }

            return response()->json([
                'status' => 'ok',
                'driver_id' => $driver->id,
                'vehicle_id' => $vehicle->id,
            ], 201);
        });
    }
}
