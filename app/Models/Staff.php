<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Staff extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $guarded = array();

    protected $fillable = [
        'username',
        'password',
        'role',
        'chain_store_id',
    ];
}
