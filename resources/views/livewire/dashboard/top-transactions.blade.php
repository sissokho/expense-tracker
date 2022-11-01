<div class="bg-white shadow-md w-full rounded-md">
    <article x-data="{tab: 0, nbTabs: Math.ceil(@js($limit) / 5)}">
        <div class="flex justify-between px-5 py-7 border-b border-gray-300">
            <h2 class="text-xl text-gray-600 font-bold">Top {{ $type->plural() }}
            </h2>

            <div class="flex gap-2">
                {{-- Arrow left circle --}}
                <span class="cursor-pointer" x-bind:class="{'text-gray-400 pointer-events-none': tab === 0}"
                    x-on:click="tab--">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11.25 9l-3 3m0 0l3 3m-3-3h7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                {{-- Arrow right circle --}}
                <span class="cursor-pointer" x-bind:class="{'text-gray-400 pointer-events-none': tab === nbTabs -1}"
                    x-on:click="tab++">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12.75 15l3-3m0 0l-3-3m3 3h-7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
        </div>

        <div class="overflow-x-auto relative p-2 sm:rounded-lg">
            @foreach ($transactions->chunk(5) as $chunk)
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
            @endforeach
        </div>
    </article>
</div>
