<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    use HasFactory;
    protected $fillable =[
        'userId',
        'otp',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */

     protected $table = 'otp_verifications';

     protected $hidden = [
        'created_at','updated_at'
     ];

}
