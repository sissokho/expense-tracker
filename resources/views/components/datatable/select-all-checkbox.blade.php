@props(['modelIds'])

<div class="flex items-center">

    <x-datatable.checkbox id="checkbox-toggle-all" x-on:click="toggleAll(event, {{  $modelIds }})" />
    <label for="checkbox-toggle-all" class="sr-only">checkbox</label>
</div>
