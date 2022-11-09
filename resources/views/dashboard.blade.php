<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden p-5 space-y-10 sm:rounded-lg">
                <livewire:dashboard.stats-cards />

                <div class="flex flex-col items-center gap-5 lg:flex-row lg:justify-between lg:items-stretch">
                    <livewire:dashboard.top-transactions :type="\App\Enums\TransactionType::Income" />
                    <livewire:dashboard.top-transactions :type="\App\Enums\TransactionType::Expense" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
