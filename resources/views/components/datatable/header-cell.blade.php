@props(['columnName', 'sortColumn', 'sortDirection'])

<th scope="col" {{ $attributes->class(['py-3 px-6']) }}>
    @if (!$attributes->has('sortable'))
    {{ $slot }}
    @else
    <div class="inline-flex items-center gap-2 cursor-pointer" wire:click="sortBy('{{ $columnName }}')">
        {{ $slot }}

        @if ($columnName === $sortColumn)
        <div>
            @if ($sortDirection === 'asc')
            {{-- Chevron Up --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-3 h-3 font-bold">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
            </svg>
            @else
            {{-- Chevron Down --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-3 h-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
            </svg>
            @endif
        </div>
        @endif
    </div>
    @endif
</th>
