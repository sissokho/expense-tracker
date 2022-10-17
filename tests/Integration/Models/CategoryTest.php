<?php

declare(strict_types=1);

namespace Tests\Integration\Models;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_searches_categories_by_their_names(): void
    {
        Category::factory()
            ->count(5)
            ->for(User::factory())
            ->state(new Sequence(
                ['name' => 'apple'],
                ['name' => 'banana'],
                ['name' => 'applause'],
                ['name' => 'milk'],
                ['name' => 'lemon'],
            ))
            ->create();

        $categories = Category::search('le')->get();

        $this->assertCount(2, $categories);
        $this->assertEquals('apple', $categories[0]->name);
        $this->assertEquals('lemon', $categories[1]->name);
    }
}
