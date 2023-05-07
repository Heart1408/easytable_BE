<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Staff extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $guarded = array();

    protected $appends = ['role'];
    protected $fillable = [
        'username',
        'password',
        'role',
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
        return 'staff';
    }
}