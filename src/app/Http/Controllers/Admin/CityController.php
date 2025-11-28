<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CityRequest;
use App\Models\City;

class CityController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(City::class, 'city');
    }

    public function index()
    {
        $items = City::orderBy('sort')->orderBy('name')->get();
        return view('admin.dicts.cities.index', compact('items'));
    }

    public function create()
    {
        $city = new City(['active' => true, 'sort' => 100]);
        return view('admin.dicts.cities.form', compact('city'));
    }

    public function store(CityRequest $request)
    {
        City::create($request->validated());
        return redirect()->route('admin.dicts.cities')->with('success', 'Город создан.');
    }

    public function edit(City $city)
    {
        return view('admin.dicts.cities.form', compact('city'));
    }

    public function update(CityRequest $request, City $city)
    {
        $city->update($request->validated());
        return redirect()->route('admin.dicts.cities')->with('success', 'Сохранено.');
    }

    public function destroy(City $city)
    {
        $city->delete();
        return back()->with('success', 'Удалено.');
    }

    public function toggle(City $city)
    {
        $this->authorize('toggle', $city);
        $city->active = !$city->active;
        $city->save();

        return back()->with('success', 'Статус изменён.');
    }
}
