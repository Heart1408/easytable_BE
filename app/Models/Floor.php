<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    use HasFactory;

    protected $fillable = [
        'chain_store_id',
        'name',
    ];

    public function tables()
    {
        return $this->hasMany(TableDetail::class);
    }
}