<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

/**
 * @property-read User $user
 */
class CategoryList extends DataTable
{
    use AuthorizesRequests;

    public Category $category;

    /**
     * @var array<string, array<int, string>>
     */
    protected $rules = [
        'category.name' => ['required', 'string', 'max:255'],
    ];

    public function getUserProperty(): User
    {
        /**
         * @var User $user
         */
        $user = auth()->user();

        return $user;
    }

    public function openModalForm(?Category $category = null): void
    {
        $this->resetErrorBag();

        $this->category = $category ?? new Category();

        $this->dispatchBrowserEvent('opening-category-form');

        $this->openingModalForm = true;
    }

    public function saveCategory(): void
    {
        $this->resetErrorBag();

        if ($this->category->id !== null) {
            $this->authorize('update', $this->category);
        }

        $this->validate();

        $this->category->user_id = $this->user->id;

        if (! $this->category->save()) {
            $this->dangerBannerEvent('Error');

            $this->openingModalForm = false;

            return;
        }

        $this->emitSelf('model-saved');

        $this->openingModalForm = false;

        $this->successBannerEvent('Saved');
    }

    public function confirmCategoryDeletion(Category $category): void
    {
        $this->category = $category;

        $this->confirmingModelDeletion = true;
    }

    public function deleteCategory(): void
    {
        $this->authorize('delete', $this->category);

        $categoryId = $this->category->id;

        if (! $this->category->delete()) {
            $this->dangerBannerEvent('Error');

            $this->confirmingModelDeletion = false;

            return;
        }

        $this->emitSelf('model-deleted');

        $this->confirmingModelDeletion = false;

        $this->dispatchBrowserEvent('model-deleted', ['id' => $categoryId]);

        $this->successBannerEvent('Deleted');
    }

    public function deleteCategories(): void
    {
        Category::destroy($this->selectedIdsForDeletion);

        $this->emitSelf('models-deleted');

        $this->reset(['confirmingModelDeletion', 'massDeletion', 'selectedIdsForDeletion']);

        $this->successBannerEvent('Deleted');
    }

    public function render(): View
    {
        return view('livewire.category-list', [
            'categories' => $this->user->categories()
                ->search($this->search)
                ->orderBy($this->sortColumn, $this->sortDirection)
                ->paginate($this->perPage),
        ]);
    }
}
