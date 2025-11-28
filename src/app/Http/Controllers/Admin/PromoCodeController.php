<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PromoCodeRequest;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromoCodeController extends Controller
{
    public function index(Request $req)
    {
        $q = PromoCode::query()
            ->when($req->filled('active'), fn($qq) => $qq->where('active', (bool)$req->boolean('active')))
            ->when($req->filled('type'), fn($qq) => $qq->where('type', $req->string('type')))
            ->when($req->filled('s'), function ($qq) use ($req) {
                $s = '%' . trim($req->string('s')) . '%';
                $qq->where(fn($w) => $w->where('code', 'like', $s)->orWhere('comment', 'like', $s));
            })
            ->orderByDesc('created_at');

        $items = $q->paginate(20)->withQueryString();

        return view('admin.loyalty.promocodes.index', compact('items'));
    }

    public function create()
    {
        $pc = new PromoCode(['active' => true, 'type' => 'bonus_fixed', 'value' => 0]);
        return view('admin.loyalty.promocodes.form', compact('pc'));
    }

    public function store(PromoCodeRequest $req)
    {
        $data = $req->validated();
        $data['created_by'] = Auth::id();
        PromoCode::create($data);
        return redirect()->route('admin.loyalty.promocodes.index')->with('success', 'Промокод создан.');
    }

    public function edit(PromoCode $promo_code)
    {
        $pc = $promo_code;
        return view('admin.loyalty.promocodes.form', compact('pc'));
    }

    public function update(PromoCodeRequest $req, PromoCode $promo_code)
    {
        $promo_code->update($req->validated());
        return redirect()->route('admin.loyalty.promocodes.index')->with('success', 'Сохранено.');
    }

    public function destroy(PromoCode $promo_code)
    {
        $promo_code->delete();
        return back()->with('success', 'Удалено.');
    }

    public function toggle(PromoCode $promo_code)
    {
        $promo_code->active = ! $promo_code->active;
        $promo_code->save();
        return back()->with('success', 'Статус изменён.');
    }
}
