<?php

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
            'name' => $this->faker->text(10),
            'type' => $this->faker->randomElement(TransactionType::cases()),
            'amount' => $this->faker->numberBetween(1, 100_000),
            'category_id' => Category::factory(),
            'user_id' => User::factory(),
        ];
    }
}
