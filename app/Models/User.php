<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class User extends Authenticatable
{

    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mobile',
        'status',
        'uniqueUserId',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'mobile_verified_at' => 'datetime',
    ];
    public function cars(): BelongsToMany
    {
        // return $this->belongsToMany(cars::class, 'shared_cars', 'agent_id', 'car_id')
        //     ->withPivot('role')  // Include additional pivot columns if necessary
        //     ->wherePivot('role', 'partner');  // Filter by pivot column values if necessary
            return $this->belongsToMany(cars::class, 'shared_cars', 'agent_id','car_id')
            ->withTimestamps();
    }

}
