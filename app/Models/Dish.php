<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'size',
        'dish_type_id',
        'image',
    ];

    public function dish_type()
    {
        return $this->belongsTo(DishType::class);
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'bills')->withPivot('quantity', 'status');
    }

    public function chain_stores()
    {
        return $this->belongsToMany(ChainStore::class, 'status')->withPivot('status');
    }

    public function scopeSearch($query, $data)
    {
        if (!isset($data['categoryId']) && !isset($data['status'])) {
            $category_id = DishType::firstOrFail()->id;
            $query->where("dish_type_id", $category_id);
        }

        if (isset($data['categoryId'])) {
            $category_id = null;
            $check = DishType::find($data['categoryId']);
            if (!$check)
                $category_id = DishType::firstOrFail()->id;
            else
                $category_id = $data['categoryId'];
            $query->where("dish_type_id", $category_id);
        }

        if (isset($data['search_key'])) {
            $query->where('name', 'like', '%' . $data['search_key'] . '%');
        }

        if (isset($data['sortByPrice'])) {
            $query->orderBy('price', $data["sortByPrice"]);
        }

        if (isset($data['status'])) {
            $status = $data['status'];
            $query->whereHas('chain_stores', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }

        return $query;
    }
}