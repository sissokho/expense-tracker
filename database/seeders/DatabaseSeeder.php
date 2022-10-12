<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'email' => 'test@gmail.com',
        ]);

        Category::factory()
            ->for($user)
            ->count(10)
            ->has(
                Transaction::factory()
                    ->count(25)
                    ->for($user)
            )
            ->create();
    }
}
