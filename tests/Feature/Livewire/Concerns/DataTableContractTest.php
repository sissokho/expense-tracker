<?php

namespace Tests\Feature\Livewire\Concerns;

use App\Models\User;
use Livewire;

trait DataTableContractTest
{
    private abstract function getTestable(): array;

    /** @test */
    public function user_can_choose_the_number_of_records_to_show_per_page(): void
    {
        Livewire::actingAs(User::factory()->make());

        $testable = $this->getTestable();

        $component = Livewire::test($testable['className'], $testable['params'])
            ->set('perPage', 20);

        $component->assertSet('perPage', 20)
            ->assertPropertyWired('perPage');
    }

    /** @test */
    public function page_number_is_reset_to_one_when_user_perform_a_search(): void
    {
        Livewire::actingAs(User::factory()->make());

        $testable = $this->getTestable();

        $component = Livewire::withQueryParams(['page' => 2])
            ->test($testable['className'], $testable['params']);

        $component->assertSet('page', 2)
            ->set('search', 'banana')
            ->assertSet('search', 'banana')
            ->assertSet('page', 1);
    }

    /**
     * @test
     */
    public function form_modal_is_closed_when_component_is_first_rendered(): void
    {
        Livewire::actingAs(User::factory()->make());

        $testable = $this->getTestable();

        $component = Livewire::test($testable['className'], $testable['params']);

        $component->assertSet('openingModalForm', false);
    }

    /** @test */
    public function form_modal_can_be_opened(): void
    {
        Livewire::actingAs(User::factory()->make());

        $testable = $this->getTestable();

        $component = Livewire::test($testable['className'], $testable['params'])->call('openModalForm');

        $component->assertSet('openingModalForm', true)
            ->assertDispatchedBrowserEvent("opening-{$testable['name']}-form");
    }

    /** @test */
    public function form_modal_can_be_closed(): void
    {
        Livewire::actingAs(User::factory()->make());

        $testable = $this->getTestable();

        $component = Livewire::test($testable['className'], $testable['params'])->set('openingModalForm', true);

        $component->assertSet('openingModalForm', true)
            ->set('openingModalForm', false)
            ->assertSet('openingModalForm', false);
    }

    /** @test */
    public function user_must_confirm_when_performing_mass_deletion(): void
    {
        Livewire::actingAs(User::factory()->make());

        $testable = $this->getTestable();
        $component = Livewire::test($testable['className'], $testable['params'])->call('confirmMassDeletion');

        $component->assertSet('massDeletion', true)
            ->assertSet('confirmingModelDeletion', true);
    }
}
