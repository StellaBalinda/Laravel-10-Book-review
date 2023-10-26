<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilderr;


class Book extends Model
{
    use HasFactory;

    public function reviews () {
        return $this->hasMany(Review::class);
    }

    public function scopeTitle(Builder $query, string $title):Builder|QueryBuilderr
    {
        return $query->where('title', 'LIKE', '%'. $title .'%');
    }
    // most popular book by the number of review
    public function scopePopular(Builder $query, $from = null, $to = null):Builder|QueryBuilderr
    {
        return $query->withCount([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q,$from, $to)
            ])
                    ->orderBy('reviews_count','desc');
     }

    // Highest rated book
    public function scopeHighestRated(Builder $query, $from = null, $to = null):Builder|QueryBuilderr
    {
        return $query->withAvg([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q,$from, $to)
            ],'rating')
                    ->orderBy('reviews_avg_rating', 'asc');
    }

    public function scopeMinReviews(Builder $query, int $minReviews):Builder|QueryBuilderr
    {
        return $query->having('reviews_count','>=',$minReviews);
    }


    // reusable code block
    private function dateRangeFilter(Builder $query, $from = null, $to = null)
    {
        if($from && !$to){
            $query->where('created_at', '>=', $from);
        }elseif (!$from && $to) {
            $query->where('created_at', '<=', $to);
        }elseif ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }
    }


}
