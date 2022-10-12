<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\TransactionType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function unauthenticated_users_are_redirected_to_login_page(): void
    {
        $response = $this->get(route('expenses'));

        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     */
    public function expenses_page_can_be_rendered(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->make();

        $this->actingAs($user);

        $response = $this->get(route('expenses'));

        $response->assertStatus(200)
            ->assertViewIs('transactions')
            ->assertViewHas('type', TransactionType::Expense);
    }

    /**
     * @test
     */
    public function expenses_page_contains_transactions_livewire_component(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->make();

        $this->actingAs($user);

        $response = $this->get(route('expenses'));

        $response->assertSeeLivewire('transaction-list');
    }
}
