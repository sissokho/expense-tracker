<?php

namespace Tests\Feature;

use App\Http\Livewire\CategoryList;
use App\Models\Category;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_redirects_unauthenticated_users_to_login_page(): void
    {
        $response = $this->get(route('categories.index'));

        $response->assertRedirect(route('login'));
    }
    /**
     * @test
     */
    public function it_successfully_loads_categories_page(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->make();

        $this->actingAs($user);

        $response = $this->get(route('categories.index'));

        $response->assertStatus(200)
            ->assertViewIs('categories.index');
    }

    /**
     * @test
     */
    public function it_contains_the_category_list_livewire_component(): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->make();

        $this->actingAs($user);

        $response = $this->get(route('categories.index'));

        $response->assertSeeLivewire('category-list');
    }
}