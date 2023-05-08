<?php

namespace App\Models;

use DB;
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

    public function scopeSearch($query, $data)
    {
        $chainstore_id = $data['chainstore_id'];

        $query->select('id', 'fullname', 'phone')->whereHas('bookings', function ($query) use ($chainstore_id) {
            $query->whereHas('staff', function ($query) use ($chainstore_id) {
                $query->where('chain_store_id', '=', $chainstore_id);
            });
        })->with([
                'bookings' => function ($query) {
                    $query->orderByDesc('time');
                }
            ])->latest('updated_at');

        if (isset($data['search_key'])) {
            $query->where('fullname', 'like', '%' . $data['search_key'] . '%')
                ->orWhere('phone', 'like', '%' . $data['search_key'] . '%');
        }

        return $query->get();
    }
}