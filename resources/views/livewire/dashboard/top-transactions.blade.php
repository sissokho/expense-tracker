<div class="bg-white shadow-md w-full rounded-md">
    <article x-data="{tab: 0, nbTabs: Math.ceil(@js($transactions).length / 5)}">
        <div class="flex justify-between px-5 py-7 border-b border-gray-300">
            <h2 class="text-xl text-gray-600 font-bold">Top {{ $type->plural() }}
            </h2>

            <div class="flex gap-2">
                {{-- Arrow left circle --}}
                <span class="cursor-pointer" x-bind:class="{'text-gray-400 pointer-events-none': tab === 0}"
                    x-on:click="tab--">
                    <x-icons.arrow-left-circle />
                </span>
                {{-- Arrow right circle --}}
                <span class="cursor-pointer"
                    x-bind:class="{'text-gray-400 pointer-events-none': nbTabs === 0 || tab === nbTabs -1}"
                    x-on:click="tab++">
                    <x-icons.arrow-right-circle />
                </span>
            </div>
        </div>

        <div class="overflow-x-auto relative p-2 sm:rounded-lg">
            @forelse ($transactions->chunk(5) as $chunk)
            <div {{ $loop->index !== 1 ? 'x-cloak' : '' }} x-show="tab === @js($loop->index)">
                @foreach ($chunk as $transaction)
                <div class="flex justify-between items-center gap-2 px-2 py-3">
                    <span class="text-gray-600">{{ $transaction->name }}</span>
                    <span class="{{ $transaction->type->colors() }} font-black rounded-full px-2 py-1">
                        {{ $transaction->formatted_amount }}
                    </span>
                </div>
                @endforeach
            </div>
            @empty
            <div class="flex justify-center items-center p-3">
                <x-icons.no-data class="h-48 w-48" />
            </div>
            @endforelse
        </div>
    </article>
</div>
