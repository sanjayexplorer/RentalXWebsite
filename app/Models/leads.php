<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class leads extends Model
{
    use HasFactory;
    protected $fillable =[
        'user_id',
        'customer_name',
        'contact_number',
        'pick_up_date_time',
        'pick_up_location',
        'drop_off_date_time',
        'drop_off_location',
        'car_model',
        'car_type',
        'lead_source',
        'status'
    ];
}
