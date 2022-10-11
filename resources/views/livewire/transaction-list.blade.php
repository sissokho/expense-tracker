<div class="space-y-2">
    <div class="overflow-x-auto relative p-2 space-y-4 sm:rounded-lg">
        <div class="flex flex-col gap-4 md:flex-row md:gap-0">
            <div class="flex gap-4 ml-auto">
                <div class="flex items-center space-x-2">
                    <x-jet-label id="per-page">Per Page</x-jet-label>
                    <select wire:model="perPage" id="per-page" class="rounded-md py-1">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>
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
                            {{ $transaction->amount }}
                        </span>
                    </th>
                    <th scope="row" class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $transaction->category->name }}
                    </th>
                    <th scope="row" class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $transaction->created_at->toFormattedDateString() }}
                    </th>
                    <td class="text-right py-4 px-6 space-x-2">
                        <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                        <a href="#" class="font-medium text-red-600 dark:text-red-500 hover:underline">Delete</a>
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
</div>
