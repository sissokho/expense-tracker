<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return BelongsTo<User, Category>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param Builder<Category> $query
     * @param string $searchTerm
     *
     * @return Builder<Category>
     */
    public function scopeSearch(Builder $query, string $searchTerm): Builder
    {
        if ($searchTerm == '') {
            return $query;
        }

        return $query->where('name', 'like', "%{$searchTerm}%");
    }
}
