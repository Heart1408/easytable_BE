<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'floor_id',
        'table_type_id',
        'top',
        'left',
        'status',
    ];
}
