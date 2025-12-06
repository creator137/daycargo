<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tariff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalculatorController extends Controller
{
    /**
     * POST /api/calculate
     *
     * Вариант 1 (простой):
     *  - tariff_id   (int, required)
     *  - distance_km (float, required)
     *
     * Вариант 2 (по точкам):
     *  - tariff_id   (int, required)
     *  - points: [
     *      { lat: float, lon: float },
     *      { lat: float, lon: float },
     *      ...
     *    ]
     */
    public function calculate(Request $request): JsonResponse
    {
        // определяем, что именно пришло
        $hasPoints = $request->filled('points');

        $rules = [
            'tariff_id' => ['required', 'integer', 'exists:tariffs,id'],
        ];

        if ($hasPoints) {
            $rules['points'] = ['required', 'array', 'min:2'];
            $rules['points.*.lat'] = ['required', 'numeric'];
            $rules['points.*.lon'] = ['required', 'numeric'];
        } else {
            $rules['distance_km'] = ['required', 'numeric', 'min:0'];
        }

        $data = $request->validate($rules);

        /** @var Tariff $tariff */
        $tariff = Tariff::query()
            ->where('id', $data['tariff_id'])
            ->where('active', true)
            ->firstOrFail();

        // 1) Определяем расстояние
        if ($hasPoints) {
            $distanceKm = $this->calculatePolylineDistanceKm($data['points']);
        } else {
            $distanceKm = (float) $data['distance_km'];
        }

        // 2) Берём параметры тарифа
        $base   = (float) $tariff->base_price; // подача
        $perKm  = (float) $tariff->per_km;     // цена км (по умолчанию 40, но управляется в админке)
        $min    = (float) $tariff->min_price;  // минималка

        // 3) Считаем цену
        $rawPrice = $base + $distanceKm * $perKm;

        $finalPrice = $rawPrice;
        if ($min > 0 && $rawPrice < $min) {
            $finalPrice = $min;
        }

        return response()->json([
            'tariff_id'       => $tariff->id,
            'vehicle_type_id' => $tariff->vehicle_type_id,

            'distance_km' => round($distanceKm, 3),

            'base_price' => $base,
            'per_km'     => $perKm,
            'min_price'  => $min,

            'raw_price'   => round($rawPrice, 2),
            'final_price' => round($finalPrice, 2),
        ]);
    }

    /**
     * Считаем суммарное расстояние по полилинии (массив точек) в км.
     */
    private function calculatePolylineDistanceKm(array $points): float
    {
        if (count($points) < 2) {
            return 0.0;
        }

        $total = 0.0;

        for ($i = 1; $i < count($points); $i++) {
            $prev = $points[$i - 1];
            $curr = $points[$i];

            $total += $this->haversineDistanceKm(
                (float) $prev['lat'],
                (float) $prev['lon'],
                (float) $curr['lat'],
                (float) $curr['lon'],
            );
        }

        return $total;
    }

    /**
     * Расстояние между двумя точками по формуле гаверсинуса, км.
     */
    private function haversineDistanceKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadiusKm = 6371.0;

        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);

        $dLat = $lat2Rad - $lat1Rad;
        $dLon = $lon2Rad - $lon1Rad;

        $a = sin($dLat / 2) ** 2
            + cos($lat1Rad) * cos($lat2Rad) * sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }
}
