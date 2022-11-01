<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Dashboard;

use App\Enums\TransactionType;
use App\Http\Livewire\Dashboard\TopTransactions;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TopTransactionsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @dataProvider transactionTypeProvider
     */
    public function the_component_can_render(TransactionType $type)
    {
        $component = Livewire::test(TopTransactions::class, [
            'type' => $type,
        ]);

        $component->assertStatus(200)
            ->assertViewIs('livewire.dashboard.top-transactions');
    }

    /**
     * @test
     * @dataProvider transactionTypeProvider
     */
    public function top_transactions_are_displayed(TransactionType $type)
    {
        $user = User::factory()->create();

        $transactions = Transaction::factory()
            ->count(20)
            ->state(['type' => $type])
            ->for($user)
            ->create();

        $topTransactions = $transactions->sortByDesc('amount')
            ->take(10)
            ->map(fn (Transaction $transaction) => [$transaction->name, $transaction->formatted_amount])
            ->flatten()
            ->toArray();

        Livewire::actingAs($user);

        $component = Livewire::test(TopTransactions::class, [
            'type' => $type,
        ]);

        $component->assertSet('limit', 10)
            ->assertSeeInOrder($topTransactions);
    }

    public function transactionTypeProvider(): array
    {
        return [
            'type income' => [TransactionType::Income],
            'type expense' => [TransactionType::Expense],
        ];
    }
}
