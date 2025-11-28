<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCodeRedemption extends Model
{
    protected $fillable = [
        'promo_code_id',
        'client_id',
        'status',
        'applied_amount',
        'order_uid',
        'meta',
    ];

    protected $casts = [
        'applied_amount' => 'decimal:2',
        'meta'           => 'array',
    ];

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class);
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
