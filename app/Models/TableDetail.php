<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableDetail extends Model
{
    use HasFactory;

    const STATUS = ['ready' => 1, 'notready' => 2, 'guests' => 3];

    protected $fillable = [
        'floor_id',
        'table_type_id',
        'top',
        'left',
        'status',
    ];

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }
}