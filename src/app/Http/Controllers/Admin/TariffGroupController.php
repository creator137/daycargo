<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TariffGroupRequest;
use App\Models\TariffGroup;

class TariffGroupController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(TariffGroup::class, 'tariffGroup');
    }

    public function index()
    {
        $items = TariffGroup::orderBy('sort')->orderBy('name')->get();
        return view('admin.dicts.tariff_groups.index', compact('items'));
    }

    public function create()
    {
        $group = new TariffGroup(['active' => true, 'sort' => 100]);
        return view('admin.dicts.tariff_groups.form', compact('group'));
    }

    public function store(TariffGroupRequest $req)
    {
        TariffGroup::create($req->validated());
        return redirect()->route('admin.dicts.tariff_groups')->with('success', 'Группа создана.');
    }

    public function show(TariffGroup $tariffGroup)
    {
        abort(404);
    }

    public function edit(TariffGroup $tariffGroup)
    {
        return view('admin.dicts.tariff_groups.form', ['group' => $tariffGroup]);
    }

    public function update(TariffGroupRequest $req, TariffGroup $tariffGroup)
    {
        $tariffGroup->update($req->validated());
        return redirect()->route('admin.dicts.tariff_groups')->with('success', 'Сохранено.');
    }

    public function destroy(TariffGroup $tariffGroup)
    {
        $tariffGroup->delete();
        return redirect()->route('admin.dicts.tariff_groups')->with('success', 'Удалено.');
    }

    public function toggle(TariffGroup $tariffGroup)
    {
        $this->authorize('toggle', $tariffGroup);

        $tariffGroup->active = ! $tariffGroup->active;
        $tariffGroup->save();

        return back()->with('success', 'Статус изменён.');
    }
}
