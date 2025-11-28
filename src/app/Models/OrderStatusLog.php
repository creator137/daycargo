<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusLog extends Model
{
    protected $fillable = ['order_id', 'status_from', 'status_to', 'actor_type', 'actor_id', 'comment'];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
