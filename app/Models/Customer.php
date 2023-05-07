<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory;
    protected $appends = ['role'];
    protected $fillable = [
        'fullname',
        'phone',
        'address',
        'email',
        'birthday',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getRoleAttribute()
    {
        return 'customer';
    }
}