<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarsBookingDateStatus extends Model
{
    use HasFactory;
    protected $fillable =[
        'carId',
        'start_date',
        'end_date',
    ];
}
