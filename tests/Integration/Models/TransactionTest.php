<?php

declare(strict_types=1);

namespace Tests\Integration\Models;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function transactions_can_be_searched_by_their_names_and_category(): void
    {
        Transaction::factory()
            ->state([
                'name' => 'abricot',
            ])
            ->for(Category::factory()
                ->create([
                    'name' => 'abc',
                ]))
            ->create();

        Transaction::factory()
            ->state([
                'name' => 'boabc',
            ])
            ->for(Category::factory()
                ->create([
                    'name' => 'food',
                ]))
            ->create();

        Transaction::factory()
            ->state([
                'name' => 'random',
            ])
            ->for(Category::factory()
                ->create([
                    'name' => 'ab',
                ]))
            ->create();

        Transaction::factory()
            ->state([
                'name' => 'another random',
            ])
            ->for(Category::factory()
                ->create([
                    'name' => 'random',
                ]))
            ->create();

        $results = Transaction::search('ab')
            ->with('category')
            ->get();

        $this->assertCount(3, $results);

        $this->assertEquals('abricot', $results[0]->name);
        $this->assertEquals('boabc', $results[1]->name);

        $this->assertEquals('random', $results[2]->name);
        $this->assertEquals('ab', $results[2]->category->name);
    }

    /**
     * @test
     */
    public function with_category_name_scope_works_as_expected(): void
    {
        Transaction::factory()->create();

        $retrievedTransaction = Transaction::first();

        $this->assertNotContains('category_name', $retrievedTransaction->getAttributes());

        Transaction::truncate();

        $category = Category::factory()->create();

        Transaction::factory()
            ->for($category)
            ->create();

        $retrievedTransaction = Transaction::query()
            ->withCategoryName()
            ->first();

        $this->assertNotContains('category_name', $retrievedTransaction->getAttributes());
        $this->assertSame($category->name, $retrievedTransaction->category_name);
    }
}
