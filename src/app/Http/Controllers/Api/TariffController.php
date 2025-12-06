<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientTariff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TariffController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $city = trim((string) $request->query('city'));

        $query = ClientTariff::query()
            ->where('active', true)
            ->where('available_site', true)
            ->with('vehicleType')
            ->orderBy('sort');

        if ($city !== '') {
            $query->where(function ($q) use ($city) {
                $q->whereNull('city')
                    ->orWhere('city', $city);
            });
        }

        $items = $query->get()->map(function (ClientTariff $t) {
            return [
                'id'                => $t->id,
                'name'              => $t->name,
                'vehicle_type_id'   => $t->vehicle_type_id,
                'vehicle_type_name' => $t->vehicleType?->name,
                'city'              => $t->city,
            ];
        });

        return response()->json(['data' => $items]);
    }
}
