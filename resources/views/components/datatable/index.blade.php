<div class="space-y-2">
    <div class="overflow-x-auto relative p-2 space-y-4 sm:rounded-lg" x-data="multipleSelection"
        x-on:model-deleted.window="unselect(event.detail.id)">
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
                        placeholder="Search by name, category" wire:model.debounce.500ms="search">
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
                    wire:click.prevent="confirmMassDeletion">Delete
                    (<span x-text="(totalSelected)"></span>)
                </x-jet-button>
                <x-jet-button class="bg-indigo-700 hover:bg-indigo-800" wire:click="openModalForm">New
                </x-jet-button>
            </div>
        </div>

        {{ $table }}
    </div>

    {{ $pagination }}

    {{ $modals }}
</div>

@pushOnce('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('multipleSelection', () => ({
            selected: @entangle('selectedIdsForDeletion'),
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
                [...document.querySelectorAll('input[type="checkbox"][data-id]')].forEach(checkbox => {
                    const id = parseInt(checkbox.dataset.id);

                    checkbox.checked = this.selected.includes(id);
                });
            },
        }));
    });
</script>
@endPushOnce
