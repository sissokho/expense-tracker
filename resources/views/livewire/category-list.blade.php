<x-datatable>
    <x-slot name="table">
        @if ($categories->isEmpty())
        <x-datatable.empty-state-message>
            No Categories Found
        </x-datatable.empty-state-message>
        @else
        <x-datatable.table>
            <x-slot name="head">
                <tr>
                    <th scope="col" class="p-4">
                        <x-datatable.select-all-checkbox :model-ids="$categories->pluck('id')" />
                    </th>
                    <x-datatable.header-cell>
                        Category
                    </x-datatable.header-cell>
                    <x-datatable.header-cell>
                        Action
                    </x-datatable.header-cell>
                </tr>
            </x-slot>
            <x-slot name="body">
                @foreach ($categories as $category)
                <x-datatable.row wire:key="category-{{ $category->id }}">
                    <td class="p-4 w-4">
                        <x-datatable.select-one-checkbox :model-id="$category->id" />
                    </td>
                    <x-datatable.data-cell>
                        {{ $category->name }}
                    </x-datatable.data-cell>
                    <x-datatable.action-cell>
                        <x-datatable.action-edit wire:click.prevent="openModalForm({{ $category }})" />
                        <x-datatable.action-delete wire:click.prevent="confirmCategoryDeletion({{ $category }})" />
                    </x-datatable.action-cell>
                </x-datatable.row>
                @endforeach
            </x-slot>
        </x-datatable.table>
        @endif
    </x-slot>

    <x-slot name="pagination">
        @if ($categories->isNotEmpty())
        <div class="p-2">{{ $categories->links() }}</div>
        @endif
    </x-slot>

    <x-slot name="modals">
        {{-- New Category/Edit Category form modal --}}
        <x-jet-dialog-modal wire:model="openingModalForm">
            <x-slot name="title">
            </x-slot>
            <x-slot name="content">
                <div class="mt-4" x-data
                    x-on:opening-category-form.window="setTimeout(() => $refs.category.focus(), 250)">
                    <x-jet-input type="text" class="mt-1 block w-full" placeholder="{{ __('Category name') }}"
                        x-ref="category" wire:model.defer="category.name" wire:keydown.enter="saveCategory" />
                    <x-jet-input-error for="category.name" class="mt-2" />
                </div>
            </x-slot>
            <x-slot name="footer">
                <x-jet-secondary-button wire:click="$toggle('openingModalForm')" wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </x-jet-secondary-button>
                <x-jet-button class="ml-3" wire:click="saveCategory" wire:loading.attr="disabled">
                    {{ __('Save') }}
                </x-jet-button>
            </x-slot>
        </x-jet-dialog-modal>

        {{-- Delete Category confirmation Modal --}}
        <x-jet-dialog-modal wire:model="confirmingModelDeletion">
            <x-slot name="title">
                @if ($massDeletion)
                {{ __('Delete Categories') }}
                @else
                {{ __('Delete Category') }}
                @endif
            </x-slot>
            <x-slot name="content">
                @if ($massDeletion)
                {{ __('Are you sure you want to delete these categories? Once these categories are deleted, all of
                their
                related
                transactions
                will be permanently deleted.') }}
                @else
                {{ __('Are you sure you want to delete this category? Once this category is deleted, all of its
                related
                transactions
                will be permanently deleted.') }}
                @endif
            </x-slot>
            <x-slot name="footer">
                <x-jet-secondary-button wire:click="$toggle('confirmingModelDeletion')" wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </x-jet-secondary-button>
                @if ($massDeletion)
                <x-jet-danger-button class="ml-3" wire:click="deleteCategories" wire:loading.attr="disabled">
                    {{ __('Delete Categories') }}
                </x-jet-danger-button>
                @else
                <x-jet-danger-button class="ml-3" wire:click="deleteCategory" wire:loading.attr="disabled">
                    {{ __('Delete Category') }}
                </x-jet-danger-button>
                @endif
            </x-slot>
        </x-jet-dialog-modal>
    </x-slot>
</x-datatable>
