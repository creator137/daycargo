<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AddressSearchController extends Controller
{
    /**
     * GET /api/address-search?query=...
     *
     * Вход:
     *  - query (string, 2+ символа) — то, что ввёл пользователь
     *
     * Выход:
     *  {
     *    "data": [
     *      { "label": "...", "lat": 55.75, "lon": 37.61 },
     *      ...
     *    ]
     *  }
     */
    public function search(Request $request): JsonResponse
    {
        $data = $request->validate([
            'query' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        $query = trim($data['query']);

        // Запрос к Nominatim (OpenStreetMap)
        $response = Http::withHeaders([
            // у Nominatim обязательный User-Agent
            'User-Agent' => 'DayCargoDemo/1.0 (+https://daycargo.ru)',
        ])
            ->get('https://nominatim.openstreetmap.org/search', [
                'q'              => $query . ', Москва', // жёстко подсужаем к Москве
                'format'         => 'jsonv2',
                'addressdetails' => 1,
                'limit'          => 5,
                'countrycodes'   => 'ru',
            ]);

        if (! $response->ok()) {
            // На демо просто отдаём пустой список, чтобы фронт не падал
            return response()->json(['data' => []]);
        }

        $items = collect($response->json())
            ->map(function (array $item) {
                return [
                    'label' => $item['display_name'] ?? ($item['address']['road'] ?? 'Адрес'),
                    'lat'   => isset($item['lat']) ? (float) $item['lat'] : null,
                    'lon'   => isset($item['lon']) ? (float) $item['lon'] : null,
                ];
            })
            ->filter(fn($i) => $i['lat'] !== null && $i['lon'] !== null)
            ->values();

        return response()->json([
            'data' => $items,
        ]);
    }
}
