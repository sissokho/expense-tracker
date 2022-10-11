<?php

namespace App\Http\Livewire;

use App\Enums\TransactionType;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * @property-read User $user
 */
class TransactionList extends Component
{
    use WithPagination;

    public TransactionType $type;

    public int $perPage = 10;

    public function mount(TransactionType $type): void
    {
        $this->type = $type;
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
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
                ->paginate($this->perPage)
        ]);
    }
}
