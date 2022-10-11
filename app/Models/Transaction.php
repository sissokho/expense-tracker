<?php

namespace App\Models;

use App\Enums\TransactionType;
use App\ValueObjects\Dollar;
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
        'amount' => 'integer',
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
    public function amount(): Attribute
    {
        return Attribute::make(
            get: fn (float $value) => new Dollar($value),
            set: fn (Dollar $value) => $value->toCents()
        );
    }
}
