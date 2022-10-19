@props(['modelId'])

<div class="flex items-center">
    <x-datatable.checkbox id="transaction-{{ $modelId }}-checkbox" x-on:change="toggle(event, {{ $modelId }})"
        data-id="{{ $modelId }}" />
    <label for="transaction-{{ $modelId }}-checkbox" class="sr-only">checkbox</label>
</div>
