<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class booking_payments extends Model
{
    use HasFactory;
    protected $fillable =[
        'bookingId',
        'received_refund',
        'payment_name',
        'other_payment_name',
        'amount'
    ];
}
