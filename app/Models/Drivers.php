<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drivers extends Model
{
    use HasFactory;
    protected $fillable = [
        'userId',
        'driver_name',
        'driver_mobile',
        'driver_mobile_country_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $table = 'drivers';
    protected $hidden = [
        'created_at','updated_at'
       ];
}
