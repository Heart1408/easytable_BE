<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Staff extends Authenticatable
{
    use HasApiTokens, HasFactory;
    const PERMISSION_ADMIN = 1;
    protected $guarded = array();

    protected $table = 'staffs';
    protected $appends = ['role'];
    protected $fillable = [
        'username',
        'password',
        'fullname',
        'email',
        'phone',
        'permission',
        'chain_store_id',
    ];

    public function chainstore()
    {
        return $this->belongsTo(ChainStore::class, 'chain_store_id', 'id');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function getRoleAttribute()
    {
        return $this->isAdmin() ? 'admin' : 'staff';
    }

    public function isAdmin()
    {
        return $this->permission === self::PERMISSION_ADMIN;
    }

    public function scopeSearch($query, $data)
    {
        if (isset($data['search_key'])) {
            $query->where('fullname', 'like', '%' . $data['search_key'] . '%')
                ->orWhere('username', 'like', '%' . $data['search_key'] . '%')
                ->orWhere('phone', $data['search_key'])
                ->orWhere('email', 'like', '%' . $data['search_key'] . '%')
                ->orWhere('permission', $data['search_key']);
        }

        return $query;
    }
}