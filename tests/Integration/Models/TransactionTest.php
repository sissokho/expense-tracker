<?php

declare(strict_types=1);

namespace Tests\Integration\Models;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\ValueObjects\Dollar;
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

    /**
     * @test
     */
    public function total_income_and_expenses_are_correctly_calculated(): void
    {
        $transactions = Transaction::factory()
            ->count(10)
            ->create();

        $expectedIncome = $transactions->where('type', TransactionType::Income)->sum('amount');
        $expectedExpenses = $transactions->where('type', TransactionType::Expense)->sum('amount');

        ['total_income' => $totalIncome, 'total_expenses' => $totalExpenses] = Transaction::query()
            ->totalIncomeAndExpenses()
            ->first()
            ->toArray();

        // Casting to int because it can return null in case there is no expense or income in the database
        $this->assertSame((new Dollar($expectedExpenses))->toCents(), (int) $totalExpenses);
        $this->assertSame((new Dollar($expectedIncome))->toCents(), (int) $totalIncome);

        // For a specific user
        $user = User::factory()->create();

        $userTransactions = Transaction::factory()
            ->for($user)
            ->count(10)
            ->create();

        $expectedIncome = $userTransactions->where('type', TransactionType::Income)->sum('amount');
        $expectedExpenses = $userTransactions->where('type', TransactionType::Expense)->sum('amount');

        ['total_income' => $totalIncome, 'total_expenses' => $totalExpenses] = Transaction::query()
            ->totalIncomeAndExpenses()
            ->where('user_id', $user->id)
            ->first()
            ->toArray();

        $this->assertSame((new Dollar($expectedExpenses))->toCents(), (int) $totalExpenses);
        $this->assertSame((new Dollar($expectedIncome))->toCents(), (int) $totalIncome);
    }
}
