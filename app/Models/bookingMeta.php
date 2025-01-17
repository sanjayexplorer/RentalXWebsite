<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bookingMeta extends Model
{
    use HasFactory;
    protected $fillable = [
        'bookingId',
        'meta_key',
        'meta_value',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $table = 'cars_booking_meta';
    protected $hidden = [
        'created_at','updated_at'
       ];
}
