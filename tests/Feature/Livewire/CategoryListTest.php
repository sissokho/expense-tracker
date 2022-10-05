<?php

namespace Tests\Feature\Livewire;

use App\Http\Livewire\CategoryList;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_renders_the_component(): void
    {
        Livewire::actingAs(User::factory()->make());

        $component = Livewire::test(CategoryList::class);

        $component->assertStatus(200);
    }

    /**
     * @test
     */
    public function it_displays_the_categories(): void
    {
        $user = User::factory()->create();

        $categories = Category::factory()
            ->for($user)
            ->count(13)
            ->create();

        Livewire::actingAs($user);

        $component = Livewire::test(CategoryList::class);

        $component->assertSee($categories[0]->name)
            ->assertSee($categories[2]->name)
            ->assertSee($categories[4]->name)
            ->assertSee($categories[9]->name);
    }

    /**
     * @test
     */
    public function it_only_displays_categories_of_the_current_logged_in_user(): void
    {
        $user = User::factory()->create();

        $userCategories = Category::factory()
            ->for($user)
            ->count(3)
            ->state(new Sequence(
                ['name' => 'fitness'],
                ['name' => 'food'],
                ['name' => 'transportation'],
            ))
            ->create();

        $otherCategories = Category::factory()
            ->for(User::factory())
            ->count(3)
            ->state(new Sequence(
                ['name' => 'health'],
                ['name' => 'school'],
            ))
            ->create();

        Livewire::actingAs($user);

        $component = Livewire::test(CategoryList::class);

        $component->assertSee($userCategories[0]->name)
            ->assertSee($userCategories[1]->name)
            ->assertSee($userCategories[2]->name)
            ->assertDontSee($otherCategories[0]->name)
            ->assertDontSee($otherCategories[1]->name);
    }

    /**
     * @test
     */
    public function it_paginates_through_the_categories(): void
    {
        $user = User::factory()->create();

        $categories = Category::factory()
            ->for($user)
            ->count(13)
            ->create();

        $shoesCategory = Category::factory()
            ->for($user)
            ->create(['name' => 'shoes']);

        Livewire::actingAs($user);

        $component = Livewire::withQueryParams(['page' => 2])
            ->test(CategoryList::class);

        $component->assertSet('page', 2)
            ->assertDontSee($categories[0]->name)
            ->assertDontSee($categories[9]->name)
            ->assertSee($categories[10]->name)
            ->assertSee($shoesCategory->name);
    }

    /**
     * @test
     */
    public function it_updates_pagination_according_to_users_choice(): void
    {
        Livewire::actingAs(User::factory()->make());

        $component = Livewire::test(CategoryList::class)
            ->set('perPage', 20);

        $component->assertSet('perPage', 20)
            ->assertPropertyWired('perPage');
    }

    /**
     * @test
     */
    public function it_performs_search(): void
    {
        $user = User::factory()->create();

        Category::factory()
            ->for($user)
            ->create(['name' => 'banana']);

        Category::factory()
            ->for($user)
            ->create(['name' => 'apple']);

        Livewire::actingAs($user);

        $component = Livewire::test(CategoryList::class)
            ->set('search', 'bana');

        $component->assertSet('search', 'bana')
            ->assertSee('banana')
            ->assertDontSee('apple')
            ->assertPropertyWired('search');
    }

    /**
     * @test
     */
    public function it_resets_page_number_to_one_when_a_search_is_perform(): void
    {
        $user = User::factory()->create();

        Category::factory()
            ->for($user)
            ->count(49)
            ->create();

        Livewire::actingAs($user);

        $component = Livewire::withQueryParams(['page' => 2])
            ->test(CategoryList::class);

        $component->assertSet('page', 2)
            ->set('search', 'banana')
            ->assertSet('search', 'banana')
            ->assertSet('page', 1);
    }
}
