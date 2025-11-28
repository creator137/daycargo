<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrgTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'type',        // topup|debit
        'amount',
        'comment',
        'performed_by', // user_id
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
