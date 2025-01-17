<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarBooking extends Model
{
    use HasFactory;
    protected $fillable =[
        'carId',
        'booking_type',
        'car_name',
        'registration_number',
        'customer_name',
        'user_id',
        'customer_mobile',
        'alt_customer_mobile',
        'customer_email',
        'customer_city',
        'pickup_location',
        'dropoff_location',
        'pickup_date',
        'start_date',
        'pickup_time',
        'dropoff_date',
        'end_date',
        'dropoff_time',
        'advance_booking_amount',
        'collected_time',
        'delivered_time',
        'status',
        'per_day_rental_charges',
        'number_of_days',
        'pickup_charges',
        'dropoff_charges',
        'discount',
        'bookingId',
        'total_booking_amount',
        'refundable_security_deposit',
        'due_at_delivery',
        'booking_remarks',
        'agent_commission',
        'agent_commission_received',
        'customer_mobile_country_code',
        'alt_customer_mobile_country_code',
        'booking_owner_id',
        'driver_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

