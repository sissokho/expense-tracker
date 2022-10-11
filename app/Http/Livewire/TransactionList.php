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

    public string $search = '';

    public int $perPage = 10;

    /**
     * @var array<string, array<string, string>>
     */
    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function mount(TransactionType $type): void
    {
        $this->type = $type;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
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
                ->where('type', $this->type)
                ->search($this->search)
                ->with('category')
                ->latest()
                ->paginate($this->perPage)
        ]);
    }
}
