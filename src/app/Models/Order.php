<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class Order extends Model
{
    protected $fillable = [
        'number',
        'city',
        'city_id',
        'type',
        'source',
        'priority',
        'status',
        'assigned_at',
        'started_at',
        'finished_at',
        'canceled_at',
        'client_id',
        'organization_id',
        'payer_type',
        'contact_name',
        'contact_phone',
        'blacklist_check',
        'from_address',
        'from_lat',
        'from_lng',
        'from_floor',
        'from_entrance',
        'from_comment',
        'to_address',
        'to_lat',
        'to_lng',
        'to_floor',
        'to_entrance',
        'to_comment',
        'arrival_window_from',
        'arrival_window_to',
        'via_points',
        'tariff_id',
        'vehicle_type_id',
        'driver_group_id',
        'options',
        'distance_km_est',
        'duration_min_est',
        'driver_id',
        'vehicle_id',
        'assign_strategy',
        'broadcast_radius_km',
        'broadcast_sent_at',
        'calc_schema',
        'price_base',
        'price_surge',
        'price_options',
        'price_waiting',
        'price_loading',
        'price_other',
        'price_discount',
        'promo_discount',
        'bonus_spent',
        'price_total',
        'currency',
        'payment_method',
        'prepaid_amount',
        'paid_amount',
        'debt_amount',
        'receipt_number',
        'promo_code_id',
        'need_terminal',
        'need_docs',
        'fragile',
        'lift_required',
        'helper_count',
        'is_return_trip',
        'comment',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'canceled_at' => 'datetime',
        'arrival_window_from' => 'datetime',
        'arrival_window_to' => 'datetime',
        'via_points' => 'array',
        'options' => 'array',
        'distance_km_est' => 'decimal:2',
        'price_base' => 'decimal:2',
        'price_surge' => 'decimal:2',
        'price_options' => 'decimal:2',
        'price_waiting' => 'decimal:2',
        'price_loading' => 'decimal:2',
        'price_other' => 'decimal:2',
        'price_discount' => 'decimal:2',
        'promo_discount' => 'decimal:2',
        'bonus_spent' => 'decimal:2',
        'price_total' => 'decimal:2',
        'prepaid_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'debt_amount' => 'decimal:2',
        'broadcast_radius_km' => 'decimal:1',
        'need_terminal' => 'bool',
        'need_docs' => 'bool',
        'fragile' => 'bool',
        'lift_required' => 'bool',
        'is_return_trip' => 'bool',
        'blacklist_check' => 'bool',
    ];

    // Связи
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    public function cityRef()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
    public function tariff()
    {
        return $this->belongsTo(Tariff::class);
    }
    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }
    public function driverGroup()
    {
        return $this->belongsTo(DriverGroup::class);
    }
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function payments()
    {
        return $this->hasMany(OrderPayment::class);
    }
    public function events()
    {
        return $this->hasMany(OrderEvent::class);
    }
    public function attachments()
    {
        return $this->hasMany(OrderAttachment::class);
    }
    public function rating()
    {
        return $this->hasOne(OrderRating::class);
    }
}
