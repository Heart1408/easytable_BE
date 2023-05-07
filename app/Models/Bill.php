<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    const STATUS = ['not_ordered' => 1, 'ordered' => 2, 'servered' => 3];

    protected $fillable = [
        'booking_id',
        'dish_id',
        'quantity',
        'status',
    ];
}