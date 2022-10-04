<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeSearch(Builder $query, string $searchTerm): Builder
    {
        if ($searchTerm == '') {
            return $query;
        }

        return $query->where('name', 'like', "%{$searchTerm}%");
    }
}
