<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Enums\TransactionType;
use App\Http\Livewire\TransactionList;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TransactionListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_can_be_rendered_with_expenses(): void
    {
        Livewire::actingAs(User::factory()->make());

        $component = Livewire::test(TransactionList::class, [
            'type' => TransactionType::Expense,
        ]);

        $component->assertStatus(200)
            ->assertSet('type', TransactionType::Expense);
    }

    /** @test */
    public function the_component_can_be_rendered_with_incomes(): void
    {
        Livewire::actingAs(User::factory()->make());

        $component = Livewire::test(TransactionList::class, [
            'type' => TransactionType::Income,
        ]);

        $component->assertStatus(200)
            ->assertSet('type', TransactionType::Income);
    }

    /**
     * @test
     */
    public function user_can_see_his_incomes(): void
    {
        $user = User::factory()->create();

        $transactions = Transaction::factory()
            ->income()
            ->for($user)
            ->for(
                Category::factory()
                    ->recycle($user)
            )
            ->count(9)
            ->create();

        $expense = Transaction::factory()
            ->expense()
            ->for($user)
            ->create([
                'name' => 'banana',
            ]);

        Livewire::actingAs($user);

        $component = Livewire::test(TransactionList::class, [
            'type' => TransactionType::Income,
        ]);

        $component->assertSee($transactions[8]->name)
            ->assertSee($transactions[4]->name)
            ->assertSee($transactions[2]->name)
            ->assertSee($transactions[0]->name)
            ->assertDontSee($expense->name);
    }

    /**
     * @test
     */
    public function user_can_see_his_expenses(): void
    {
        $user = User::factory()->create();

        $transactions = Transaction::factory()
            ->expense()
            ->for($user)
            ->for(
                Category::factory()
                    ->recycle($user)
            )
            ->count(9)
            ->create();

        $income = Transaction::factory()
            ->income()
            ->for($user)
            ->create([
                'name' => 'banana',
            ]);

        Livewire::actingAs($user);

        $component = Livewire::test(TransactionList::class, [
            'type' => TransactionType::Expense,
        ]);

        $component->assertSee($transactions[0]->name)
            ->assertSee($transactions[2]->name)
            ->assertSee($transactions[4]->name)
            ->assertSee($transactions[8]->name)
            ->assertDontSee($income->name);
    }

    /**
     * @test
     */
    public function only_current_logged_in_user_expenses_are_displayed(): void
    {
        $user = User::factory()->create();

        $userTransactions = Transaction::factory()
            ->expense()
            ->for($user)
            ->count(3)
            ->state(new Sequence(
                ['name' => 'fitness'],
                ['name' => 'food'],
                ['name' => 'transportation'],
            ))
            ->create();

        $someoneElseTransactions = Transaction::factory()
            ->expense()
            ->count(2)
            ->create();

        Livewire::actingAs($user);

        $component = Livewire::test(TransactionList::class, [
            'type' => TransactionType::Expense,
        ]);

        $component->assertSee($userTransactions[0]->name)
            ->assertSee($userTransactions[1]->name)
            ->assertSee($userTransactions[2]->name)
            ->assertDontSee($someoneElseTransactions[0]->name)
            ->assertDontSee($someoneElseTransactions[1]->name);
    }

    /**
     * @test
     */
    public function only_current_logged_in_user_incomes_are_displayed(): void
    {
        $user = User::factory()->create();

        $userTransactions = Transaction::factory()
            ->income()
            ->for($user)
            ->count(2)
            ->state(new Sequence(
                ['name' => 'salary'],
                ['name' => 'gift'],
            ))
            ->create();

        $someoneElseTransactions = Transaction::factory()
            ->income()
            ->count(2)
            ->create();

        Livewire::actingAs($user);

        $component = Livewire::test(TransactionList::class, [
            'type' => TransactionType::Income,
        ]);

        $component->assertSee($userTransactions[0]->name)
            ->assertSee($userTransactions[1]->name)
            ->assertDontSee($someoneElseTransactions[0]->name)
            ->assertDontSee($someoneElseTransactions[1]->name);
    }

    /**
     * @test
     */
    public function transactions_can_be_paginated(): void
    {
        $user = User::factory()->create();

        $categories = Transaction::factory()
            ->expense()
            ->for($user)
            ->count(10)
            ->create();

        $shoesPurchase = Transaction::factory()
            ->expense()
            ->state([
                'created_at' => now()->subYears(2),
            ])
            ->for($user)
            ->create(['name' => 'Air Jordan']);

        Livewire::actingAs($user);

        $component = Livewire::withQueryParams(['page' => 2])
            ->test(TransactionList::class, [
                'type' => TransactionType::Expense,
            ]);

        $component->assertSet('page', 2)
            ->assertDontSee($categories[0]->name)
            ->assertDontSee($categories[9]->name)
            ->assertSee($shoesPurchase->name);
    }

    /**
     * @test
     */
    public function user_can_choose_the_number_of_transactions_to_show_per_page(): void
    {
        Livewire::actingAs(User::factory()->make());

        $component = Livewire::test(TransactionList::class, [
            'type' => TransactionType::Income,
        ])
            ->set('perPage', 20);

        $component->assertSet('perPage', 20)
            ->assertPropertyWired('perPage');
    }

    /**
     * @test
     */
    public function user_can_search_transactions_by_their_name_and_their_category(): void
    {
        $user = User::factory()->create();

        $transactions[] = Transaction::factory()
            ->expense()
            ->state([
                'name' => 'abricot',
                'created_at' => now()->subMinute(),
            ])
            ->for($user)
            ->for(Category::factory()
                ->create([
                    'name' => 'abc',
                ]))
            ->create();

        $transactions[] = Transaction::factory()
            ->expense()
            ->state([
                'name' => 'boabc',
                'created_at' => now(),
            ])
            ->for($user)
            ->for(Category::factory()
                ->create([
                    'name' => 'food',
                ]))
            ->create();

        $transactions[] = Transaction::factory()
            ->expense()
            ->state([
                'name' => 'random',
            ])
            ->for($user)
            ->for(Category::factory()
                ->create([
                    'name' => 'see',
                ]))
            ->create();

        Livewire::actingAs($user);

        $component = Livewire::test(TransactionList::class, [
            'type' => TransactionType::Expense,
        ])
            ->set('search', 'ab');

        $component->assertSet('search', 'ab')
            ->assertPropertyWired('perPage')
            ->assertSeeInOrder([
                $transactions[1]->name,
                $transactions[1]->category->name,
                $transactions[0]->name,
                $transactions[0]->category->name,
            ])
            ->assertDontSee($transactions[2]->name)
            ->assertDontSee($transactions[2]->category->name);
    }

    /**
     * @test
     */
    public function page_number_is_reset_to_one_when_user_perform_a_search(): void
    {
        Livewire::actingAs(User::factory()->make());

        $component = Livewire::withQueryParams(['page' => 2])
            ->test(TransactionList::class, [
                'type' => TransactionType::Income,
            ]);

        $component->assertSet('page', 2)
            ->set('search', 'banana')
            ->assertSet('search', 'banana')
            ->assertSet('page', 1);
    }
}
