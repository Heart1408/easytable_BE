<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChainStore extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
    ];

    public function staffs()
    {
        return $this->hasMany(Staff::class, 'chain_store_id', 'id');
    }

    public function floors()
    {
        return $this->hasMany(Floor::class, 'chain_store_id', 'id');
    }
}