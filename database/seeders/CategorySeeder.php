<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::factory()
            ->for(User::factory()->create([
                'email' => 'test@test.gmail',
            ]))
            ->count(23)
            ->create();
    }
}
