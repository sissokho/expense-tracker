<x-datatable>
    <x-slot name="table">
        @if ($transactions->isEmpty())
        <x-datatable.empty-state-message>
            No {{ $type->plural() }} Found
        </x-datatable.empty-state-message>
        @else
        <x-datatable.table>
            <x-slot name="head">
                <tr>
                    <th scope="col" class="p-4">
                        <x-datatable.select-all-checkbox :model-ids="$transactions->pluck('id')" />
                    </th>
                    <x-datatable.header-cell>
                        Name
                    </x-datatable.header-cell>
                    <x-datatable.header-cell>
                        Amount
                    </x-datatable.header-cell>
                    <x-datatable.header-cell>
                        Category
                    </x-datatable.header-cell>
                    <x-datatable.header-cell>
                        Entry Date
                    </x-datatable.header-cell>
                    <x-datatable.header-cell>
                        Action
                    </x-datatable.header-cell>
                </tr>
            </x-slot>
            <x-slot name="body">
                @foreach ($transactions as $transaction)
                <x-datatable.row wire:key="transaction-{{ $transaction->id }}">
                    <td class="p-4 w-4">
                        <x-datatable.select-one-checkbox :model-id="$transaction->id" />
                    </td>
                    <x-datatable.data-cell>
                        {{ $transaction->name }}
                    </x-datatable.data-cell>
                    <x-datatable.data-cell>
                        <span class="{{ $transaction->type->colors() }} font-black rounded-full px-2 py-1">
                            {{ $transaction->formatted_amount }}
                        </span>
                    </x-datatable.data-cell>
                    <x-datatable.data-cell>
                        {{ $transaction->category->name }}
                    </x-datatable.data-cell>
                    <x-datatable.data-cell>
                        {{ $transaction->created_at->toFormattedDateString() }}
                    </x-datatable.data-cell>
                    <x-datatable.action-cell>
                        <x-datatable.action-edit wire:click.prevent="openModalForm({{ $transaction }})" />
                        <x-datatable.action-delete
                            wire:click.prevent="confirmTransactionDeletion({{ $transaction }})" />
                    </x-datatable.action-cell>
                </x-datatable.row>
                @endforeach
            </x-slot>
        </x-datatable.table>
        @endif
    </x-slot>

    <x-slot name="pagination">
        @if ($transactions->isNotEmpty())
        <div class="p-2">{{ $transactions->links() }}</div>
        @endif
    </x-slot>

    <x-slot name="modals">
        {{-- New Transaction/Edit Transaction form modal --}}
        <x-jet-dialog-modal wire:model="openingModalForm">
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
                <x-jet-secondary-button wire:click="$toggle('openingModalForm')" wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </x-jet-secondary-button>

                <x-jet-button class="ml-3" wire:click.prevent="saveTransaction" wire:loading.attr="disabled">
                    {{ __('Save') }}
                </x-jet-button>
            </x-slot>
        </x-jet-dialog-modal>

        {{-- Delete Transaction confirmation Modal --}}
        <x-jet-dialog-modal wire:model="confirmingModelDeletion">
            <x-slot name="title">
                @if ($massDeletion)
                {{ __('Delete Transactions') }}
                @else
                {{ __('Delete Transaction') }}
                @endif
            </x-slot>

            <x-slot name="content">
                @if ($massDeletion)
                {{ __('Are you sure you want to delete these transactions') }}
                @else
                {{ __('Are you sure you want to delete this transaction?') }}
                @endif
            </x-slot>

            <x-slot name="footer">
                <x-jet-secondary-button wire:click="$toggle('confirmingModelDeletion')" wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </x-jet-secondary-button>

                @if ($massDeletion)
                <x-jet-danger-button class="ml-3" wire:click="deleteTransactions" wire:loading.attr="disabled">
                    {{ __('Delete Transactions') }}
                </x-jet-danger-button>
                @else
                <x-jet-danger-button class="ml-3" wire:click="deleteTransaction" wire:loading.attr="disabled">
                    {{ __('Delete Transaction') }}
                </x-jet-danger-button>
                @endif
            </x-slot>
        </x-jet-dialog-modal>
    </x-slot>
</x-datatable>
