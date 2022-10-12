<?php

namespace App\Models;

use App\Casts\Dollar;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $guarded = [];

    protected $casts = [
        'type' => TransactionType::class,
        'amount' => Dollar::class,
    ];

    /**
     * @return BelongsTo<Category, Transaction>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo<User, Transaction>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // /**
    //  * @return Attribute<callable, callable>
    //  */
    // public function amount(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn (float $value) => new Dollar($value),
    //         set: fn (Dollar $value) => $value->toCents()
    //     );
    // }

    /**
     * @param  Builder<Transaction>  $query
     * @param  string  $searchTerm
     * @return Builder<Transaction>
     */
    public function scopeSearch(Builder $query, string $searchTerm): Builder
    {
        if ($searchTerm == '') {
            return $query;
        }

        return $query->where('name', 'like', "%{$searchTerm}%")
            ->orWhereRelation('category', 'name', 'like', "%{$searchTerm}%");
    }
}
