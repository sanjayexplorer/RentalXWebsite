<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class carImages extends Model
{
    use HasFactory;
    protected $fillable=[
        'carId',
        'imageId',
        'status',
        'featured',
    ];
}
