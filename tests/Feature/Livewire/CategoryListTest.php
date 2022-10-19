<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Http\Livewire\CategoryList;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_can_be_rendered(): void
    {
        Livewire::actingAs(User::factory()->make());

        $component = Livewire::test(CategoryList::class);

        $component->assertStatus(200);
    }

    /**
     * @test
     */
    public function user_can_see_his_categories(): void
    {
        $user = User::factory()->create();

        $categories = Category::factory()
            ->for($user)
            ->count(10)
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
    public function only_current_logged_in_user_categories_are_displayed(): void
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
    public function categories_can_be_paginated(): void
    {
        $user = User::factory()->create();

        $recentCategories = Category::factory()
            ->for($user)
            ->count(10)
            ->sequence(fn ($sequence) => ['name' => "Category {$sequence->index}"])
            ->create();

        $olderCategories = Category::factory()
            ->for($user)
            ->count(2)
            ->state(new Sequence(
                ['created_at' => now()->subDay()]
            ))
            ->create();

        Livewire::actingAs($user);

        $component = Livewire::withQueryParams(['page' => 2])
            ->test(CategoryList::class);

        $component->assertSet('page', 2)
            ->assertSee($olderCategories[0]->name)
            ->assertSee($olderCategories[1]->name);

        $recentCategories->each(fn (Category $category) => $component->assertDontSee($category->name));
    }

    /**
     * @test
     */
    public function user_can_choose_the_number_of_categories_to_show_per_page(): void
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
    public function user_can_search_categories_by_their_name(): void
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
    public function page_number_is_reset_to_one_when_user_perform_a_search(): void
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

    /**
     * @test
     */
    public function form_components_are_correctly_wired(): void
    {
        Livewire::actingAs(User::factory()->make());

        $component = Livewire::test(CategoryList::class);

        $component->assertPropertyWired('category.name')
            ->assertMethodWired('openModalForm')
            ->assertMethodWired('saveCategory');
    }

    /**
     * @test
     */
    public function form_modal_is_closed_when_component_is_first_rendered(): void
    {
        Livewire::actingAs(User::factory()->make());

        $component = Livewire::test(CategoryList::class);

        $component->assertSet('openingModalForm', false);
    }

    /**
     * @test
     */
    public function form_can_be_opened(): void
    {
        Livewire::actingAs(User::factory()->make());

        $component = Livewire::test(CategoryList::class)
            ->call('openModalForm');

        $component->assertSet('openingModalForm', true)
            ->assertDispatchedBrowserEvent('opening-category-form');
    }

    /**
     * @test
     */
    public function category_property_is_empty_when_form_modal_is_opened_in_creation_mode(): void
    {
        Livewire::actingAs(User::factory()->make());

        $component = Livewire::test(CategoryList::class)
            ->call('openModalForm');

        $component->assertSet('category.id', null)
            ->assertSet('category.name', null);
    }

    /**
     * @test
     */
    public function category_property_is_set_when_form_modal_is_opened_in_edit_mode(): void
    {
        $user = User::factory()->create();

        $category = Category::factory()
            ->for($user)
            ->create();

        Livewire::actingAs($user);

        $component = Livewire::test(CategoryList::class)
            ->call('openModalForm', $category);

        $component->assertSet('category.id', $category->id)
            ->assertSet('category.name', $category->name);
    }

    /**
     * @test
     */
    public function form_can_be_closed(): void
    {
        Livewire::actingAs(User::factory()->make());

        $component = Livewire::test(CategoryList::class)
            ->set('openingModalForm', true);

        $component->assertSet('openingModalForm', true)
            ->set('openingModalForm', false)
            ->assertSet('openingModalForm', false);
    }

    /**
     * @test
     */
    public function user_can_create_a_category(): void
    {
        Livewire::actingAs(User::factory()->create());

        $component = Livewire::test(CategoryList::class)
            ->call('openModalForm')
            ->set('category.name', 'Health')
            ->call('saveCategory');

        $component->assertSet('openingModalForm', false)
            ->assertEmitted('model-saved')
            ->assertDispatchedBrowserEvent('banner-message', [
                'style' => 'success',
                'message' => 'Saved',
            ]);

        $this->assertDatabaseCount('categories', 1);
        $this->assertDatabaseHas('categories', [
            'id' => 1,
            'name' => 'Health',
        ]);
    }

    /**
     * @test
     */
    public function user_can_edit_a_category(): void
    {
        $user = User::factory()->create();

        $category = Category::factory()
            ->for($user)
            ->create([
                'name' => 'Healt',
            ]);

        Livewire::actingAs($user);

        $component = Livewire::test(CategoryList::class)
            ->call('openModalForm', $category)
            ->set('category.name', 'Health')
            ->call('saveCategory');

        $component->assertSet('openingModalForm', false)
            ->assertEmitted('model-saved')
            ->assertDispatchedBrowserEvent('banner-message', [
                'style' => 'success',
                'message' => 'Saved',
            ]);

        $this->assertDatabaseCount('categories', 1);
        $this->assertDatabaseHas('categories', [
            'id' => 1,
            'name' => 'Health',
        ]);
    }

    /**
     * @test
     */
    public function user_cannot_edit_a_category_that_does_not_belong_to_him(): void
    {
        $category = Category::factory()
            ->for(User::factory()->create())
            ->create([
                'name' => 'Healt',
            ]);

        Livewire::actingAs(User::factory()->make());

        $component = Livewire::test(CategoryList::class)
            ->call('openModalForm', $category)
            ->set('category.name', 'Health')
            ->call('saveCategory');

        $component->assertForbidden();
    }

    /**
     * @test
     */
    public function user_cannot_save_a_category_with_an_empty_name(): void
    {
        Livewire::actingAs(User::factory()->make());

        $component = Livewire::test(CategoryList::class)
            ->call('openModalForm')
            ->set('category.name', '')
            ->call('saveCategory');

        $component->assertHasErrors([
            'category.name' => ['required'],
        ]);
    }

    /**
     * @test
     */
    public function user_cannot_save_a_category_with_a_name_that_is_longer_than_255_characters(): void
    {
        Livewire::actingAs(User::factory()->make());

        $component = Livewire::test(CategoryList::class)
            ->call('openModalForm')
            ->set('category.name', Str::of('h')->repeat(256))
            ->call('saveCategory');

        $component->assertHasErrors([
            'category.name' => ['max'],
        ]);
    }

    /**
     * @test
     */
    public function user_must_confirm_when_deleting_a_category(): void
    {
        $user = User::factory()->create();

        $category = Category::factory()
            ->for($user)
            ->create();

        Livewire::actingAs($user);

        $component = Livewire::test(CategoryList::class)
            ->call('confirmCategoryDeletion', $category);

        $component->assertMethodWired('confirmCategoryDeletion')
            ->assertSet('confirmingModelDeletion', true);
    }

    /**
     * @test
     */
    public function user_can_delete_one_of_his_category(): void
    {
        $user = User::factory()->create();

        $category = Category::factory()
            ->for($user)
            ->create();

        // Related transactions will be deleted as well
        Transaction::factory()
            ->for($user)
            ->for($category)
            ->create();

        Livewire::actingAs($user);

        $component = Livewire::test(CategoryList::class)
            ->call('confirmCategoryDeletion', $category)
            ->call('deleteCategory');

        $component->assertSet('category.id', $category->id)
            ->assertSet('confirmingModelDeletion', false)
            ->assertEmitted('model-deleted')
            ->assertDispatchedBrowserEvent('model-deleted', ['id' => $category->id])
            ->assertDispatchedBrowserEvent('banner-message', [
                'style' => 'success',
                'message' => 'Deleted',
            ]);

        $this->assertModelMissing($category);
        $this->assertDatabaseCount('categories', 0);
        $this->assertDatabaseCount('transactions', 0);
    }

    /**
     * @test
     */
    public function user_cannot_delete_a_category_that_does_not_belong_to_him(): void
    {
        $category = Category::factory()
            ->for(User::factory())
            ->create();

        Livewire::actingAs(User::factory()->make());

        $component = Livewire::test(CategoryList::class)
            ->call('confirmCategoryDeletion', $category)
            ->call('deleteCategory');

        $component->assertForbidden();
    }

    /**
     * @test
     */
    public function user_must_confirm_when_deleting_multiple_categories(): void
    {
        Livewire::actingAs(User::factory()->make());

        $component = Livewire::test(CategoryList::class)
            ->call('confirmMassDeletion');

        $component->assertSet('massDeletion', true)
            ->assertSet('confirmingModelDeletion', true);
    }

    /**
     * @test
     */
    public function user_can_delete_multiple_categories(): void
    {
        $user = User::factory()->create();

        Category::factory()
            ->count(10)
            ->for($user)
            ->create();

        $selectedIdsForDeletion = [1, 2, 4];

        Livewire::actingAs($user);

        $component = Livewire::test(CategoryList::class)
            ->set('selectedIdsForDeletion', $selectedIdsForDeletion)
            ->call('confirmMassDeletion')
            ->call('deleteCategories');

        $component->assertSet('confirmingModelDeletion', false)
            ->assertSet('massDeletion', false)
            ->assertSet('selectedIdsForDeletion', [])
            ->assertEmitted('models-deleted')
            ->assertDispatchedBrowserEvent('banner-message', [
                'style' => 'success',
                'message' => 'Deleted',
            ]);

        $this->assertDatabaseCount('categories', 7);
        $this->assertDatabaseMissing('categories', [
            'id' => 1,
        ]);
        $this->assertDatabaseMissing('categories', [
            'id' => 2,
        ]);
        $this->assertDatabaseMissing('categories', [
            'id' => 4,
        ]);
    }
}
