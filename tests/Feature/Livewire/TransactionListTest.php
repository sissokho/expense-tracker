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
use Illuminate\Support\Str;
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

    /**
     * @test
     */
    public function form_components_are_wired(): void
    {
        Livewire::actingAs(User::factory()->make());

        $component = Livewire::test(TransactionList::class, [
            'type' => TransactionType::Income,
        ]);

        $component->assertPropertyWired('transaction.name')
            ->assertPropertyWired('transaction.amount')
            ->assertPropertyWired('transaction.category_id')
            ->assertMethodWired('openModalForm')
            ->assertMethodWired('saveTransaction');
    }

    /**
     * @test
     */
    public function form_modal_is_closed_when_component_is_first_rendered(): void
    {
        Livewire::actingAs(User::factory()->make());

        $component = Livewire::test(TransactionList::class, [
            'type' => TransactionType::Expense,
        ]);

        $component->assertSet('openingModalForm', false);
    }

    /**
     * @test
     */
    public function form_modal_can_be_opened(): void
    {
        Livewire::actingAs(User::factory()->make());

        $component = Livewire::test(TransactionList::class, [
            'type' => TransactionType::Income,
        ])->call('openModalForm');

        $component->assertSet('openingModalForm', true)
            ->assertDispatchedBrowserEvent('opening-transaction-form');
    }

    /**
     * @test
     */
    public function transaction_property_is_empty_when_form_modal_is_opened_in_creation_mode(): void
    {
        Livewire::actingAs(User::factory()->make());

        $component = Livewire::test(TransactionList::class, [
            'type' => TransactionType::Expense,
        ])->call('openModalForm');

        $component->assertSet('transaction.id', null)
            ->assertSet('transaction.name', null)
            ->assertSet('transaction.amount', null)
            ->assertSet('transaction.category_id', null);
    }

    /**
     * @test
     */
    public function transaction_property_is_set_when_form_modal_is_opened_in_edit_mode(): void
    {
        $user = User::factory()->create();

        $transaction = Transaction::factory()
            ->for($user)
            ->create();

        Livewire::actingAs($user);

        $component = Livewire::test(TransactionList::class, [
            'type' => TransactionType::Income,
        ])->call('openModalForm', $transaction);

        $component->assertSet('transaction.id', $transaction->id)
            ->assertSet('transaction.name', $transaction->name)
            ->assertSet('transaction.amount', $transaction->amount)
            ->assertSet('transaction.category_id', $transaction->category_id);
    }

    /**
     * @test
     */
    public function form_modal_can_be_closed(): void
    {
        Livewire::actingAs(User::factory()->make());

        $component = Livewire::test(TransactionList::class, [
            'type' => TransactionType::Expense,
        ])->set('openingModalForm', true);

        $component->assertSet('openingModalForm', true)
            ->set('openingModalForm', false)
            ->assertSet('openingModalForm', false);
    }

    /**
     * @test
     * @dataProvider transactionTypeProvider
     */
    public function user_can_create_a_transaction(TransactionType $type): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user);

        $category = Category::factory()
            ->for($user)
            ->create();

        $component = Livewire::test(TransactionList::class, [
            'type' => $type,
        ])
            ->call('openModalForm')
            ->set('transaction.name', 'Banana')
            ->set('transaction.amount', 1) // 1 USD
            ->set('transaction.category_id', $category->id)
            ->call('saveTransaction');

        $component->assertSet('openingModalForm', false)
            ->assertEmitted('model-saved')
            ->assertDispatchedBrowserEvent('banner-message', [
                'style' => 'success',
                'message' => 'Saved',
            ]);

        $this->assertDatabaseCount('transactions', 1);
        $this->assertDatabaseHas('transactions', [
            'id' => 1,
            'name' => 'Banana',
            'amount' => 100, // 100 Cents => 1 USD
            'category_id' => $category->id,
            'type' => $type->value,
        ]);
    }

    /**
     * @test
     * @dataProvider transactionTypeProvider
     */
    public function user_can_edit_a_transaction(TransactionType $type): void
    {
        $user = User::factory()->create();

        $foodCategory = Category::factory()
            ->for($user)
            ->create([
                'name' => 'Food',
            ]);

        $transaction = Transaction::factory()
            ->for($user)
            ->for($foodCategory)
            ->create([
                'name' => 'Bana',
                'amount' => '2', // 2 USD
            ]);

        $fruitCategory = Category::factory()
            ->for($user)
            ->create([
                'name' => 'Fruit',
            ]);

        Livewire::actingAs($user);

        $component = Livewire::test(TransactionList::class, [
            'type' => $type,
        ])->call('openModalForm', $transaction)
            ->set('transaction.name', 'Banana')
            ->set('transaction.amount', 1) // 1 USD
            ->set('transaction.category_id', $fruitCategory->id)
            ->call('saveTransaction');

        $component->assertSet('openingModalForm', false)
            ->assertEmitted('model-saved')
            ->assertDispatchedBrowserEvent('banner-message', [
                'style' => 'success',
                'message' => 'Saved',
            ]);

        $this->assertDatabaseCount('transactions', 1);
        $this->assertDatabaseHas('transactions', [
            'id' => 1,
            'name' => 'Banana',
            'amount' => 100, // 100 Cents => 1 USD
            'category_id' => $fruitCategory->id,
            'type' => $type,
        ]);
    }

    /**
     * @test
     */
    public function user_cannot_edit_a_category_that_does_not_belong_to_him(): void
    {
        $transaction = Transaction::factory()
            ->for(User::factory()->create())
            ->create();

        Livewire::actingAs(User::factory()->make());

        $component = Livewire::test(TransactionList::class, [
            'type' => TransactionType::Income,
        ])->call('openModalForm', $transaction)
            ->set('transaction.name', 'Health')
            ->set('transaction.amount', 1)
            ->call('saveTransaction');

        $component->assertForbidden();
    }

    /**
     * @test
     * @dataProvider invalidInputsProvider
     */
    public function user_cannot_save_a_transaction_with_invalid_data(array $inputs, string $property, string $rule): void
    {
        $user = User::factory()->create();

        Category::factory()
            ->for($user)
            ->create();

        if ($rule === 'in') {
            Category::factory()->create();
        }

        Livewire::actingAs($user);

        $component = Livewire::test(TransactionList::class, [
            'type' => TransactionType::Expense,
        ])->call('openModalForm')
            ->set('transaction.name', $inputs[0])
            ->set('transaction.amount', $inputs[1])
            ->set('transaction.category_id', $inputs[2])
            ->call('saveTransaction');

        $component->assertHasErrors([
            "transaction.{$property}" => [$rule],
        ]);
    }

    /**
     * @test
     * @dataProvider transactionTypeProvider
     */
    public function user_must_confirm_when_deleting_a_transaction(TransactionType $type): void
    {
        $user = User::factory()->create();

        $transaction = Transaction::factory()
            ->for($user)
            ->create([
                'type' => $type,
            ]);

        Livewire::actingAs($user);

        $component = Livewire::test(TransactionList::class, [
            'type' => $type,
        ])->call('confirmTransactionDeletion', $transaction);

        $component->assertMethodWired('confirmTransactionDeletion')
            ->assertSet('confirmingModelDeletion', true);
    }

    /**
     * @test
     * @dataProvider transactionTypeProvider
     */
    public function user_can_delete_one_of_his_transactions(TransactionType $type): void
    {
        $user = User::factory()->create();

        $transaction = Transaction::factory()
            ->for($user)
            ->create([
                'type' => $type,
            ]);

        Livewire::actingAs($user);

        $component = Livewire::test(TransactionList::class, [
            'type' => $type,
        ])->call('confirmTransactionDeletion', $transaction)
            ->call('deleteTransaction');

        $component->assertSet('transaction.id', $transaction->id)
            ->assertSet('confirmingModelDeletion', false)
            ->assertEmitted('model-deleted')
            ->assertDispatchedBrowserEvent('model-deleted', ['id' => $transaction->id])
            ->assertDispatchedBrowserEvent('banner-message', [
                'style' => 'success',
                'message' => 'Deleted',
            ]);

        $this->assertModelMissing($transaction);
        $this->assertDatabaseCount('transactions', 0);
    }

    /**
     * @test
     * @dataProvider transactionTypeProvider
     */
    public function user_cannot_delete_a_transaction_that_does_not_belong_to_him(TransactionType $type): void
    {
        $transaction = Transaction::factory()
            ->for(User::factory())
            ->create([
                'type' => $type,
            ]);

        Livewire::actingAs(User::factory()->make());

        $component = Livewire::test(TransactionList::class, [
            'type' => $type,
        ])->call('confirmTransactionDeletion', $transaction)
            ->call('deleteTransaction');

        $component->assertForbidden();
    }

    /**
     * @test
     */
    public function user_must_confirm_when_deleting_multiple_transactions(): void
    {
        Livewire::actingAs(User::factory()->make());

        $component = Livewire::test(TransactionList::class, [
            'type' => TransactionType::Income,
        ])->call('confirmMassDeletion');

        $component->assertSet('massDeletion', true)
            ->assertSet('confirmingModelDeletion', true);
    }

    /**
     * @test
     * @dataProvider transactionTypeProvider
     */
    public function user_can_delete_multiple_transactions(TransactionType $type): void
    {
        $user = User::factory()->create();

        Transaction::factory()
            ->count(10)
            ->for($user)
            ->create([
                'type' => $type,
            ]);

        $selectedIdsForDeletion = [1, 2, 4];

        Livewire::actingAs($user);

        $component = Livewire::test(TransactionList::class, [
            'type' => $type,
        ])->set('selectedIdsForDeletion', $selectedIdsForDeletion)
            ->call('confirmMassDeletion')
            ->call('deleteTransactions');

        $component->assertSet('confirmingModelDeletion', false)
            ->assertSet('massDeletion', false)
            ->assertSet('selectedIdsForDeletion', [])
            ->assertEmitted('models-deleted')
            ->assertDispatchedBrowserEvent('banner-message', [
                'style' => 'success',
                'message' => 'Deleted',
            ]);

        $this->assertDatabaseCount('transactions', 7);
        $this->assertDatabaseMissing('transactions', [
            'id' => 1,
        ]);
        $this->assertDatabaseMissing('transactions', [
            'id' => 2,
        ]);
        $this->assertDatabaseMissing('transactions', [
            'id' => 4,
        ]);
    }

    public function invalidInputsProvider(): array
    {
        // Format: [[name, amount, category_id], property to test against, validation error rule]
        return [
            'empty name' => [['', 10, 1], 'name', 'required'],
            'name is longer than 255 characters' => [[Str::of('h')->repeat(256), 10, 1], 'name', 'max'],
            'amount is not numeric' => [['banana', 'dd', 1], 'amount', 'min'],
            'amount is lower than 0.01 USD or 1 Cent' => [['banana', 0, 1], 'amount', 'min'],
            'category id is null' => [['banana', 10, ''], 'category_id', 'required'],
            'category id is not numeric' => [['banana', 10, 'id'], 'category_id', 'numeric'],
            'category id is not an integer' => [['banana', 10, 1.5], 'category_id', 'integer'],
            'category does not belongs to the user' => [['banana', 10, 2], 'category_id', 'in'],
            'category does not exists' => [['banana', 10, 10], 'category_id', 'in'],
        ];
    }

    public function transactionTypeProvider(): array
    {
        return [
            'type income' => [TransactionType::Income],
            'type expense' => [TransactionType::Expense],
        ];
    }
}
