<?php

declare(strict_types=1);

namespace App\Http\Livewire\Dashboard;

use App\Models\Transaction;
use App\Models\User;
use App\ValueObjects\Money;
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
            // Convert to int because total_income/total_expenses can return null in case their are no income or expense in the database
            'total_income' => Money::fromCents((int) $totalIncome),
            'total_expenses' => Money::fromCents((int) $totalExpenses),
            'profit' => Money::fromCents($profit),
        ]);
    }
}
