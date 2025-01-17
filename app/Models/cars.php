<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cars extends Model
{
    use HasFactory;
    protected $fillable =[
        'name',
        'registration_number',
        'model',
        'transmission',
        'fuel_type',
        'manufacturing_year',
        'car_type',
        'sunroof',
        'price',
        'seats',
        // 'color',
        'user_id',
        'status',
    ];
    public function users(): BelongsToMany
    {
        // return $this->belongsToMany(User::class, 'shared_cars', 'car_id', 'agent_id')
        //     ->withPivot('role')  // Include additional pivot columns if necessary
        //     ->wherePivot('role', 'partner');  // Filter by pivot column values if necessary
            return $this->belongsToMany(User::class, 'shared_cars', 'agent_id', 'car_id')
            ->withTimestamps();
    }
}
