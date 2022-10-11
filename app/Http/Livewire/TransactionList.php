<?php

namespace App\Http\Livewire;

use App\Enums\TransactionType;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Component;

/**
 * @property-read User $user
 */
class TransactionList extends Component
{
    public TransactionType $type;

    public function mount(TransactionType $type): void
    {
        $this->type = $type;
    }

    public function getUserProperty(): User
    {
        /**
         * @var User $user
         */
        $user = auth()->user();

        return $user;
    }

    public function render(): View
    {
        return view('livewire.transaction-list', [
            'transactions' => $this->user->transactions()
                ->with('category')
                ->where('type', $this->type)
                ->latest()
                ->get()
        ]);
    }
}
