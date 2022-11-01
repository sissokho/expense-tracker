<div class="flex flex-col gap-5 font-semibold sm:flex-row">
    <article class="flex flex-col justify-center gap-2 px-5 bg-white w-full h-28 rounded-md shadow-md">
        <h2>Total Income</h2>
        <span class="text-2xl text-green-700">{{ $total_income }}</span>
    </article>
    <article class="flex flex-col justify-center gap-2 px-5 bg-white w-full h-28 rounded-md shadow-md">
        <h2>Total Expenses</h2>
        <span class="text-2xl text-red-700">{{ $total_expenses }}</span>
    </article>
    <article class="flex flex-col justify-center gap-2 px-5 bg-white w-full h-28 rounded-md shadow-md">
        <h2>Profit</h2>
        <span class="text-2xl {{ $profit->isPositive() > 0 ? 'text-green-700' : 'text-red-700' }}">{{ $profit }}</span>
    </article>
</div>
