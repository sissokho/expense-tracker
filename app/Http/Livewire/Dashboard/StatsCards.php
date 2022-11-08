<?php

declare(strict_types=1);

namespace App\Http\Livewire\Dashboard;

use App\Models\Transaction;
use App\Models\User;
use App\ValueObjects\Dollar;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class StatsCards extends Component
{
    public function render(): View
    {
        /** @var User $user */
        $user = auth()->user();

        /** @var Transaction $aggregates */
        $aggregates = $user->transactions()
            ->totalIncomeAndExpenses()
            ->first();

        ['total_income' => $totalIncome, 'total_expenses' => $totalExpenses] = $aggregates->toArray();

        $profit = $totalIncome - $totalExpenses;

        return view('livewire.dashboard.stats-cards', [
            'total_income' => Dollar::fromCents((int) $totalIncome),
            'total_expenses' => Dollar::fromCents((int) $totalExpenses),
            'profit' => Dollar::fromCents($profit),
        ]);
    }
}
