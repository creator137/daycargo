<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'code', 'title', 'qty', 'price', 'total', 'meta'];
    protected $casts = ['qty' => 'decimal:2', 'price' => 'decimal:2', 'total' => 'decimal:2', 'meta' => 'array'];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
