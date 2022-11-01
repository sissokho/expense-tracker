<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function dashboard_is_only_accessible_to_only_authenticated_user()
    {
        $response = fn (): TestResponse => $this->get(route('dashboard'));

        $response()->assertRedirect(route('login'));

        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();

        $this->actingAs($user);

        $response()->assertOk();
    }

    /** @test */
    public function dashboard_view_is_correctly_rendered(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->make();

        $this->actingAs($user);

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200)
            ->assertViewIs('dashboard');
    }

    /** @test */
    public function dashboard_page_contains_the_correct_livewire_components(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->make();

        $this->actingAs($user);

        $response = $this->get(route('dashboard'));

        $response->assertSeeLivewire('dashboard.stats-cards')
            ->assertSeeLivewire('dashboard.top-transactions');
    }
}
