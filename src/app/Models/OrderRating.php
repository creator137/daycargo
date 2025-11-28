<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderRating extends Model
{
    protected $fillable = ['order_id', 'client_id', 'rating', 'comment'];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
