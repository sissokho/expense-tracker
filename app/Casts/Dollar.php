<?php

declare(strict_types=1);

namespace App\Casts;

use App\ValueObjects\Dollar as DollarValueObject;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Dollar implements CastsAttributes
{
    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  int  $value
     * @param  array<string, mixed>  $attributes
     * @return float
     */
    public function get($model, string $key, $value, array $attributes): float
    {
        return DollarValueObject::fromCents($value)->amount;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array<string, mixed>  $attributes
     * @return ?int
     */
    public function set($model, string $key, $value, array $attributes): ?int
    {
        if (! is_null($value) && ! is_numeric($value)) {
            return null;
        }

        return (new DollarValueObject((float) $value))->toCents();
    }
}
