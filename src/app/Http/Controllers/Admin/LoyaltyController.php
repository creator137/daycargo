<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use App\Models\PromoCodeRedemption;
use App\Models\Referral;

class LoyaltyController extends Controller
{
    public function __construct()
    {
        // Дублируем защиту на случай прямого вызова без роута
        $this->middleware('can:promocodes.view');
    }

    public function index()
    {
        $recentCodes = PromoCode::orderByDesc('created_at')->limit(10)->get();
        $recentRed   = PromoCodeRedemption::with(['promoCode', 'client'])
            ->orderByDesc('created_at')->limit(10)->get();
        $recentRefs  = Referral::with(['referrer', 'referee'])
            ->orderByDesc('created_at')->limit(10)->get();

        return view('admin.loyalty.index', compact('recentCodes', 'recentRed', 'recentRefs'));
    }
}
