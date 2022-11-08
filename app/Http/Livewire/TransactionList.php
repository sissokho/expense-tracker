<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * @property-read User $user
 * @property-read Collection<int, Category> $categories
 */
class TransactionList extends DataTable
{
    use AuthorizesRequests;

    public TransactionType $type;

    public Transaction $transaction;

    /**
     * @var array<string, string>
     */
    protected $messages = [
        'transaction.category_id.in' => 'Please select one of your categories.',
    ];

    public function mount(TransactionType $type): void
    {
        $this->type = $type;
    }

    /**
     * @return array<string, array<int, \Illuminate\Validation\Rules\In|\Illuminate\Validation\Rules\Unique|string>>
     */
    public function rules(): array
    {
        return [
            'transaction.name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('transactions', 'name')
                    ->where(fn (Builder $query) => $query->where('user_id', $this->user->id)->where('type', $this->type)),
            ],
            'transaction.amount' => ['required', 'numeric', 'min:0.01'], //0.01 => 1 cent,
            'transaction.category_id' => [
                'required',
                'numeric',
                'integer',
                Rule::in($this->categories->pluck('id')),
            ],
        ];
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

    public function openModalForm(?Transaction $transaction = null): void
    {
        $this->resetErrorBag();

        $this->transaction = $transaction ?? new Transaction();

        $this->dispatchBrowserEvent('opening-transaction-form');

        $this->openingModalForm = true;
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
            $this->dangerBannerEvent('Error');

            $this->openingModalForm = false;

            return;
        }

        $this->emitSelf('model-saved');

        $this->openingModalForm = false;

        $this->successBannerEvent('Saved');
    }

    public function confirmTransactionDeletion(Transaction $transaction): void
    {
        $this->transaction = $transaction;

        $this->confirmingModelDeletion = true;
    }

    public function deleteTransaction(): void
    {
        $this->authorize('delete', $this->transaction);

        $transactionId = $this->transaction->id;

        if (! $this->transaction->delete()) {
            $this->dangerBannerEvent('Error');

            $this->confirmingModelDeletion = false;

            return;
        }

        $this->emitSelf('model-deleted');

        $this->confirmingModelDeletion = false;

        $this->dispatchBrowserEvent('model-deleted', ['id' => $transactionId]);

        $this->successBannerEvent('Deleted');
    }

    public function deleteTransactions(): void
    {
        Transaction::destroy($this->selectedIdsForDeletion);

        $this->emitSelf('models-deleted');

        $this->reset(['confirmingModelDeletion', 'massDeletion', 'selectedIdsForDeletion']);

        $this->successBannerEvent('Deleted');
    }

    public function render(): View
    {
        return view('livewire.transaction-list', [
            'transactions' => $this->user->transactions()
                ->withCategoryName()
                ->where('type', $this->type)
                ->search($this->search)
                ->orderBy($this->sortColumn, $this->sortDirection)
                ->paginate($this->perPage),
        ]);
    }
}
