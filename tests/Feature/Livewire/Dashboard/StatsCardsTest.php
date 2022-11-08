<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Dashboard;

use App\Http\Livewire\Dashboard\StatsCards;
use App\Models\Transaction;
use App\Models\User;
use App\ValueObjects\Dollar;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StatsCardsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_can_render(): void
    {
        Livewire::actingAs(User::factory()->make());

        $component = Livewire::test(StatsCards::class);

        $component->assertStatus(200)
            ->assertViewIs('livewire.dashboard.stats-cards');
    }

    /** @test */
    public function the_stats_are_correctly_displayed_for_the_current_logged_in_user(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user);

        $component = Livewire::test(StatsCards::class);

        $component->assertViewHas('total_income', Dollar::fromCents(0))
            ->assertViewHas('total_expenses', Dollar::fromCents(0))
            ->assertViewHas('profit', Dollar::fromCents(0))
            ->assertSeeHtmlInOrder([
                '<h2>Total Income</h2>',
                '<span class="text-2xl text-green-700">$0.00</span>',
                '<h2>Total Expenses</h2>',
                '<span class="text-2xl text-red-700">$0.00</span>',
                '<h2>Profit</h2>',
                '<span class="text-2xl text-red-700">$0.00</span>',
            ]);

        /* -------- */

        Transaction::factory()
            ->count(3)
            ->income()
            ->state(new Sequence(
                ['amount' => 20],
                ['amount' => 55],
                ['amount' => 2.5]
            ))
            ->for($user)
            ->create();

        Transaction::factory()
            ->count(3)
            ->expense()
            ->state(new Sequence(
                ['amount' => 10],
                ['amount' => 40],
                ['amount' => 2]
            ))
            ->for($user)
            ->create();

        $component = Livewire::test(StatsCards::class);

        $totalIncome = (int) ((20 * 100) + (55 * 100) + (2.5 * 100)); // Convert from USD to Cents
        $totalExpense = (int) ((10 * 100) + (40 * 100) + (2 * 100));
        $profit = $totalIncome - $totalExpense;

        $component->assertViewHas('total_income', Dollar::fromCents($totalIncome))
            ->assertViewHas('total_expenses', Dollar::fromCents($totalExpense))
            ->assertViewHas('profit', Dollar::fromCents($profit))
            ->assertSeeHtmlInOrder([
                '<h2>Total Income</h2>',
                '<span class="text-2xl text-green-700">$77.50</span>',
                '<h2>Total Expenses</h2>',
                '<span class="text-2xl text-red-700">$52.00</span>',
                '<h2>Profit</h2>',
                '<span class="text-2xl text-green-700">$25.50</span>',
            ]);
    }
}
