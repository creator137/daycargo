<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CancelReasonRequest;
use App\Models\CancelReason;

class CancelReasonController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(CancelReason::class, 'cancelReason');
    }

    public function index()
    {
        $reasons = CancelReason::orderBy('sort')->orderBy('title')->get();
        return view('admin.dicts.cancel_reasons.index', compact('reasons'));
    }

    public function create()
    {
        $reason = new CancelReason([
            'active' => true,
            'window_minutes' => 10,
            'initiator' => 'customer',
            'sort' => 100,
        ]);
        return view('admin.dicts.cancel_reasons.form', compact('reason'));
    }

    public function store(CancelReasonRequest $request)
    {
        CancelReason::create($request->validated());
        return redirect()->route('admin.dicts.cancel_reasons')->with('success', 'Причина создана.');
    }

    public function edit(CancelReason $cancelReason)
    {
        return view('admin.dicts.cancel_reasons.form', ['reason' => $cancelReason]);
    }

    public function update(CancelReasonRequest $request, CancelReason $cancelReason)
    {
        $cancelReason->update($request->validated());
        return redirect()->route('admin.dicts.cancel_reasons')->with('success', 'Сохранено.');
    }

    public function destroy(CancelReason $cancelReason)
    {
        $cancelReason->delete();
        return redirect()->route('admin.dicts.cancel_reasons')->with('success', 'Удалено.');
    }

    public function toggle(CancelReason $cancelReason)
    {
        $this->authorize('toggle', $cancelReason);

        $cancelReason->active = ! $cancelReason->active;
        $cancelReason->save();

        return back()->with('success', 'Статус изменён.');
    }
}
