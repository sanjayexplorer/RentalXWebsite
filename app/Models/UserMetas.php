<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMetas extends Model
{
    use HasFactory;
    protected $fillable = [
        'userId',
        'meta_key',
        'meta_value',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $table = 'user_metas';
    protected $hidden = [
        'created_at','updated_at'
       ];
}
