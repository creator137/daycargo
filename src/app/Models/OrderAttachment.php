<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAttachment extends Model
{
    protected $fillable = ['order_id', 'path', 'type', 'size', 'created_by'];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
