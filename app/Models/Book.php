<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    public function reviews () {
        return $this->hasMany(Review::class);
    }

    public function scopeTitle(Builder $query, string $title):Builder
    {
        return $query->where('title', 'LIKE', '%'. $title .'%');
    }
    // most popular book by the number of review
    public function scopePopular(Builder $query):Builder
    {
        return $query->withCount('reviews')
                    ->orderBy('reviews_count','desc');
    }

    // Highest rated book
    public function scopeHighestRated(Builder $query):Builder
    {
        return $query->withAvg('reviews','rating')
                    ->orderBy('reviews_avg_rating', 'asc');
    }


}
