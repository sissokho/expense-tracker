<div class="space-y-2">
    <div class="overflow-x-auto relative p-2 space-y-4 sm:rounded-lg" x-data="multipleSelection"
        x-on:category-deleted.window="unselect(event.detail.id)">
        <div class="flex flex-col gap-4 md:flex-row md:gap-0">
            <div class="bg-white dark:bg-gray-900">
                <label for="table-search" class="sr-only">Search</label>
                <div class="relative mt-1">
                    <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor"
                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <input type="text" id="table-search"
                        class="block p-2 pl-10 w-80 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Search for categories" wire:model.debounce.500ms="search">
                </div>
            </div>
            <div class="flex gap-4 ml-auto">
                <div class="flex items-center space-x-2">
                    <x-jet-label id="per-page">Per Page</x-jet-label>
                    <select wire:model="perPage" id="per-page" class="rounded-md py-1">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <x-jet-button class="bg-red-700 hover:bg-red-800" style="display: none;" x-show="totalSelected"
                    wire:click.prevent="confirmMassCategoryDeletion">Delete
                    (<span x-text="(totalSelected)"></span>)
                </x-jet-button>
                <x-jet-button class="bg-indigo-700 hover:bg-indigo-800" wire:click="openCategoryForm">New
                </x-jet-button>
            </div>
        </div>
        @if ($categories->isEmpty())
        <div class="text-center text-gray-500">
            No Categories Found
        </div>
        @else
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="p-4">
                        <div class="flex items-center">
                            <input id="checkbox-toggle-all" type="checkbox"
                                class="w-4 h-4 text-blue-600 bg-gray-100 rounded border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                x-on:click="toggleAll(event, @js($categories->pluck('id')))">
                            <label for="checkbox-toggle-all" class="sr-only">checkbox</label>
                        </div>
                    </th>
                    <th scope="col" class="py-3 px-6">
                        Category
                    </th>
                    <th scope="col" class="py-3 px-6 text-right">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                    wire:key="category-{{ $category->id }}">
                    <td class="p-4 w-4">
                        <div class="flex items-center" wire:key="category-checkbox-{{ $category->id }}">
                            <input id="category-{{ $category->id }}-checkbox" type="checkbox"
                                x-on:change="toggle(event, @js($category->id))" data-id="{{ $category->id }}"
                                class="checkbox w-4 h-4 text-blue-600 bg-gray-100 rounded border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="category-{{ $category->id }}-checkbox" class="sr-only">checkbox</label>
                        </div>
                    </td>
                    <th scope="row" class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $category->name }}
                    </th>
                    <td class="text-right py-4 px-6 space-x-2">
                        <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
                            wire:click.prevent="openCategoryForm({{ $category }})">Edit</a>
                        <a href="#" class="font-medium text-red-600 dark:text-red-500 hover:underline"
                            wire:click.prevent="confirmCategoryDeletion({{ $category }})">Delete</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    @if ($categories->isNotEmpty())
    <div class="p-2">{{ $categories->links() }}</div>
    @endif

    {{-- New Category/Edit Category form modal --}}
    <x-jet-dialog-modal wire:model="openingCategoryForm">
        <x-slot name="title">

        </x-slot>

        <x-slot name="content">
            <div class="mt-4" x-data x-on:opening-category-form.window="setTimeout(() => $refs.category.focus(), 250)">
                <x-jet-input type="text" class="mt-1 block w-full" placeholder="{{ __('Category name') }}"
                    x-ref="category" wire:model.defer="category.name" wire:keydown.enter="saveCategory" />

                <x-jet-input-error for="category.name" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('openingCategoryForm')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>

            <x-jet-button class="ml-3" wire:click="saveCategory" wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>

    {{-- Delete Category confirmation Modal --}}
    <x-jet-dialog-modal wire:model="confirmingCategoryDeletion">
        <x-slot name="title">
            @if ($massDeletion)
            {{ __('Delete Categories') }}
            @else
            {{ __('Delete Category') }}
            @endif
        </x-slot>

        <x-slot name="content">
            @if ($massDeletion)
            {{ __('Are you sure you want to delete these categories? Once these categories are deleted, all of their
            related
            transactions
            will be permanently deleted.') }}
            @else
            {{ __('Are you sure you want to delete this category? Once this category is deleted, all of its related
            transactions
            will be permanently deleted.') }}
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('confirmingCategoryDeletion')" wire:loading.attr="disabled">
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
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('multipleSelection', () => ({
            selected: @entangle('selectedCategories'),
            totalSelected: 0,
            toggleAllCheckbox: document.getElementById('checkbox-toggle-all'),

            init() {
                this.$watch('selected', () => {
                    this.totalSelected = this.selected.length;
                });
            },

            unselect(id) {
                this.selected = this.selected.filter(itemId => id !== itemId);

                this.toggleAllCheckbox.checked = false;
            },

            toggle(event, id) {
                if (event.target.checked) {
                    this.selected.push(id);
                } else {
                    this.unselect(id);
                }
            },

            toggleAll(event, ids) {
                if (event.target.checked) {
                    this.selected = ids;
                } else {
                    this.selected = [];
                }

                this.toggleCheckboxes();
            },

            toggleCheckboxes() {
                [...document.querySelectorAll('.checkbox')].forEach(checkbox => {
                    const id = parseInt(checkbox.dataset.id);

                    checkbox.checked = this.selected.includes(id);
                });
            },
        }));
    });
</script>
@endpush
