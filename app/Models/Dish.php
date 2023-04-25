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
    ];

    public function dish_type()
    {
        return $this->belongsTo(DishType::class);
    }

    public function scopeSearch($query, $data)
    {
        if (isset($data['search_key'])) {
            $query->where('name', 'like', '%'. $data['search_key'] .'%')
                ->orWhere('description', 'like', '%'. $data['search_key'] .'%');
        }

        if (isset($data['status'])) {
            $query->orderBy('id', $data['status']);
        }

        if (isset($data['teacher'])) {
            $query->whereHas('teachers', function ($subQuery) use ($data) {
                $subQuery->where('user_id', $data['teacher']);
            });
        }

        if (isset($data['number_learner'])) {
            $query->withCount('students')->orderBy('students_count', $data["number_learner"]);
        }

        if (isset($data['number_lesson'])) {
            $query->withCount('lessons')->orderBy('lessons_count', $data["number_lesson"]);
        }

        if (isset($data['time'])) {
            $query->withSum('lessons', 'time')->orderBy('lessons_sum_time', $data["time"]);
        }

        if (isset($data['tags'])) {
            $query->whereHas('tags', function ($subquery) use ($data) {
                $subquery->where('tag_id', $data['tags']);
            });
        }

        return $query;
    }
}