<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class master_images extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'url',
        'base_url',
        'status',
        'ImageUniqueId',
        'withoutPublicUrl'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $table = 'master_images';
    protected $hidden = [
        'created_at','updated_at'
       ];
}
