<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $limit = (int) $request->query('limit', 50);
        $limit = max(1, min($limit, 200));

        $cities = City::query()
            ->where('active', true)
            ->when(
                $q,
                fn($query) =>
                $query->where('name', 'like', "%{$q}%")
            )
            ->orderBy('sort')
            ->orderBy('name')
            ->limit($limit)
            ->get()
            ->map(fn(City $city) => [
                'id'   => $city->id,
                'text' => $city->name,
            ]);

        return response()->json([
            'results' => $cities,
        ]);
    }
}
