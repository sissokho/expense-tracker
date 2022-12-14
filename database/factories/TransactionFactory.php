<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->catchPhrase(),
            'type' => $this->faker->randomElement(TransactionType::cases()),
            'amount' => $this->faker->randomFloat(nbMaxDecimals: 2, min: 1, max: 100), // In dollar
            'category_id' => Category::factory(),
            'user_id' => User::factory(),
            'created_at' => $this->faker->dateTimeThisYear(),
        ];
    }

    public function expense(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => TransactionType::Expense,
            ];
        });
    }

    public function income(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => TransactionType::Income,
            ];
        });
    }
}
