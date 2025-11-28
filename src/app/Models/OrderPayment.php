<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    protected $fillable = ['order_id', 'method', 'amount', 'currency', 'status', 'provider', 'provider_txn_id', 'meta'];
    protected $casts = ['amount' => 'decimal:2', 'meta' => 'array'];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
