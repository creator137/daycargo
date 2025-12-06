<?php

namespace App\Services;

class DistanceService
{
    /**
     * Принимает массив точек маршрута:
     * [
     *   ['lat' => 58.0, 'lon' => 56.2],
     *   ['lat' => 58.1, 'lon' => 56.25],
     *   ...
     * ]
     * и возвращает суммарное расстояние в км.
     */
    public function distanceFromPoints(array $points): float
    {
        $total = 0.0;

        for ($i = 1; $i < count($points); $i++) {
            $prev = $points[$i - 1];
            $curr = $points[$i];

            $total += $this->haversine(
                (float) $prev['lat'],
                (float) $prev['lon'],
                (float) $curr['lat'],
                (float) $curr['lon'],
            );
        }

        return $total;
    }

    private function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // км

        $lat1 = deg2rad($lat1);
        $lat2 = deg2rad($lat2);
        $deltaLat = $lat2 - $lat1;
        $deltaLon = deg2rad($lon2 - $lon1);

        $a = sin($deltaLat / 2) ** 2
            + cos($lat1) * cos($lat2) * sin($deltaLon / 2) ** 2;

        $c = 2 * asin(min(1, sqrt($a)));

        return $earthRadius * $c;
    }
}
