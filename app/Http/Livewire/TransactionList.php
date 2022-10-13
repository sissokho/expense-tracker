<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * @property-read User $user
 * @property-read Collection<int, Category> $categories
 */
class TransactionList extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public TransactionType $type;

    public string $search = '';

    public int $perPage = 10;

    public bool $openingTransactionForm = false;

    public Transaction $transaction;

    public ?int $category = null;

    /**
     * @var array<string, string>
     */
    protected $messages = [
        'transaction.category_id.in' => 'Please select one of your categories.',
    ];

    /**
     * @var array<string, array<string, string>>
     */
    protected $queryString = [
        'search' => ['except' => ''],
    ];

    /**
     * @var array<string, string>
     */
    protected $listeners = [
        'transaction-saved' => '$refresh',
    ];

    public function mount(TransactionType $type): void
    {
        $this->type = $type;
    }

    /**
     * @return array<string, array<int, \Illuminate\Validation\Rules\In|string>>
     */
    public function rules(): array
    {
        return [
            'transaction.name' => ['required', 'string', 'max:255'],
            'transaction.amount' => ['required', 'numeric', 'min:0.01'], //0.01 => 1 cent,
            'transaction.category_id' => [
                'required',
                'numeric',
                'integer',
                Rule::in($this->categories->pluck('id')),
            ],
        ];
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

    /**
     * @return Collection<int, Category>
     */
    public function getCategoriesProperty(): Collection
    {
        return $this->user->categories;
    }

    /**
     * @return array<int, array{id: int, name: string}>
     */
    public function getSelectCategoriesProperty(): array
    {
        return $this->categories
            ->map(function (Category $category) {
                return ['id' => $category->id, 'name' => $category->name];
            })
            ->all();
    }

    public function openTransactionForm(Transaction $transaction = null): void
    {
        $this->resetErrorBag();

        $this->transaction = $transaction ?? new Transaction();

        $this->dispatchBrowserEvent('opening-transaction-form');

        $this->openingTransactionForm = true;
    }

    public function saveTransaction(): void
    {
        $this->resetErrorBag();

        if ($this->transaction->id !== null) {
            $this->authorize('update', $this->transaction);
        }

        $this->validate();

        $this->transaction->fill([
            'user_id' => $this->user->id,
            'type' => $this->type,
        ]);

        if (! $this->transaction->save()) {
            $this->dispatchBrowserEvent('banner-message', [
                'style' => 'danger',
                'message' => 'Error',
            ]);

            $this->openingTransactionForm = false;

            return;
        }

        $this->emitSelf('transaction-saved');

        $this->openingTransactionForm = false;

        $this->dispatchBrowserEvent('banner-message', [
            'style' => 'success',
            'message' => 'Saved',
        ]);
    }

    public function render(): View
    {
        return view('livewire.transaction-list', [
            'transactions' => $this->user->transactions()
                ->where('type', $this->type)
                ->search($this->search)
                ->with('category')
                ->latest()
                ->paginate($this->perPage),
        ]);
    }
}
