<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\Dollar;
use App\Enums\TransactionType;
use App\ValueObjects\Dollar as DollarValueObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property float $amount
 */
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

    /**
     * @return Attribute<callable, callable>
     */
    public function formattedAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => (string) new DollarValueObject($this->amount),
        );
    }

    /**
     * @param  Builder<Transaction>  $query
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

    /**
     * @param  Builder<Transaction>  $query
     */
    public function scopeWithCategoryName(Builder $query): void
    {
        $query->addSelect(
            ['category_name' => Category::select('name')
                ->whereColumn('id', 'transactions.category_id'), ]
        );
    }

    /**
     * @param  Builder<Transaction>  $query
     */
    public function scopeTotalIncomeAndExpenses(Builder $query): void
    {
        $query->selectRaw('SUM(CASE WHEN type = ? THEN amount ELSE 0 END) AS total_income', [(TransactionType::Income)->value])
            ->selectRaw('SUM(CASE WHEN type = ? THEN amount ELSE 0 END) AS total_expenses', [(TransactionType::Expense)->value]);
    }
}
