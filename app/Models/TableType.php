<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableType extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'number_chair',
    ];

    public function floors()
    {
        return $this->belongsToMany(Floor::class, 'table_details', 'table_type_id', 'floor_id')->withPivot('status', 'top', 'left');
    }
}