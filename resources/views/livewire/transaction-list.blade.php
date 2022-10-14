<div class="space-y-2">
    <div class="overflow-x-auto relative p-2 space-y-4 sm:rounded-lg">
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
                <x-jet-button class="bg-indigo-700 hover:bg-indigo-800" wire:click="openTransactionForm">New
                </x-jet-button>
            </div>
        </div>

        @if ($transactions->isEmpty())
        <div class="text-center text-gray-500">
            No {{ str($type->name)->plural() }} Found
        </div>
        @else
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="p-4">
                        <div class="flex items-center">
                            <input id="checkbox-toggle-all" type="checkbox"
                                class="w-4 h-4 text-blue-600 bg-gray-100 rounded border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="checkbox-toggle-all" class="sr-only">checkbox</label>
                        </div>
                    </th>
                    <th scope="col" class="py-3 px-6">
                        Name
                    </th>
                    <th scope="col" class="py-3 px-6">
                        Amount
                    </th>
                    <th scope="col" class="py-3 px-6">
                        Category
                    </th>
                    <th scope="col" class="py-3 px-6">
                        Entry Date
                    </th>
                    <th scope="col" class="py-3 px-6 text-right">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)
                <tr
                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td class="p-4 w-4">
                        <div class="flex items-center" wire:key="transaction-checkbox-{{ $transaction->id }}">
                            <input id="transaction-{{ $transaction->id }}-checkbox" type="checkbox"
                                class="checkbox w-4 h-4 text-blue-600 bg-gray-100 rounded border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="transaction-{{ $transaction->id }}-checkbox" class="sr-only">checkbox</label>
                        </div>
                    </td>
                    <th scope="row" class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $transaction->name }}
                    </th>
                    <th scope="row" class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        <span class="{{ $transaction->type->colors() }} font-black rounded-full px-2 py-1">
                            {{ $transaction->formatted_amount }}
                        </span>
                    </th>
                    <th scope="row" class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $transaction->category->name }}
                    </th>
                    <th scope="row" class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $transaction->created_at->toFormattedDateString() }}
                    </th>
                    <td class="text-right py-4 px-6 space-x-2">
                        <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
                            wire:click.prevent="openTransactionForm({{ $transaction }})">Edit</a>
                        <a href="#" class="font-medium text-red-600 dark:text-red-500 hover:underline"
                            wire:click.prevent="confirmTransactionDeletion({{ $transaction }})">Delete</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    @if ($transactions->isNotEmpty())
    <div class="p-2">{{ $transactions->links() }}</div>
    @endif

    {{-- New Transaction/Edit Transaction form modal --}}
    <x-jet-dialog-modal wire:model="openingTransactionForm">
        <x-slot name="title">

        </x-slot>

        <x-slot name="content">
            <div class="mt-4 space-y-4" x-data
                x-on:opening-transaction-form.window="setTimeout(() => $refs.name.focus(), 250)">
                <div>
                    <x-jet-label for="name" value="{{ __('Name') }}" />
                    <x-jet-input type="text" id="name" class="mt-1 block w-full" placeholder="{{ __('Health') }}"
                        x-ref="name" wire:model.defer="transaction.name" />
                    <x-jet-input-error for="transaction.name" class="mt-2" />
                </div>
                <div>
                    <x-jet-label for="amount" value="{{ __('Amount (in USD)') }}" />
                    <x-jet-input type="text" id="amount" inputmode="decimal" class="mt-1 block w-full"
                        placeholder="{{ __('20.5') }}" wire:model.defer="transaction.amount" />
                    <x-jet-input-error for="transaction.amount" class="mt-2" />
                </div>
                <div>
                    <x-jet-label for="category" value="{{ __('Category') }}" />
                    <select id="category" class="mt-1 block w-full text-black"
                        wire:model.defer="transaction.category_id">
                        <option value="0" selected>Select a category</option>
                        @foreach ($this->selectCategories as ['id' => $id, 'name' => $name])
                        <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <x-jet-input-error for="transaction.category_id" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('openingTransactionForm')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>

            <x-jet-button class="ml-3" wire:click.prevent="saveTransaction" wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>

    {{-- Delete Transaction confirmation Modal --}}
    <x-jet-dialog-modal wire:model="confirmingTransactionDeletion">
        <x-slot name="title">
            {{ __('Delete Transaction') }}
        </x-slot>

        <x-slot name="content">

            {{ __('Are you sure you want to delete this transaction?') }}
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('confirmingTransactionDeletion')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-3" wire:click="deleteTransaction" wire:loading.attr="disabled">
                {{ __('Delete Transaction') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>
