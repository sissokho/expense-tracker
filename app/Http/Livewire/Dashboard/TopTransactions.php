<?php

declare(strict_types=1);

namespace App\Http\Livewire\Dashboard;

use App\Enums\TransactionType;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class TopTransactions extends Component
{
    public TransactionType $type;

    public int $limit = 10;

    public function mount(TransactionType $type): void
    {
        $this->type = $type;
    }

    public function render(): View
    {
        /** @var User $user */
        $user = auth()->user();

        return view('livewire.dashboard.top-transactions', [
            'transactions' => $user->transactions()
                ->where('type', $this->type)
                ->orderByDesc('amount')
                ->take($this->limit)
                ->get(),
        ]);
    }
}
