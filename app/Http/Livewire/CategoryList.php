<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * @property-read User $user
 */
class CategoryList extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public string $search = '';
    public int $perPage = 10;
    public bool $openingCategoryForm = false;
    public bool $confirmingCategoryDeletion = false;
    public bool $massDeletion = false;
    public ?Category $category = null;
    public array $selectedCategories = [];

    protected $queryString = [
        'search' => ['except' => '']
    ];

    protected $rules = [
        'category.name' => ['required', 'string', 'max:255']
    ];

    protected $listeners = [
        'category-saved' => '$refresh',
        'category-deleted' => '$refresh',
        'categories-deleted' => '$refresh',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function getUserProperty(): User
    {
        return auth()->user();
    }

    public function openCategoryForm(Category $category = null): void
    {
        $this->resetErrorBag();

        $this->category = $category ?? new Category();

        $this->dispatchBrowserEvent('opening-category-form');

        $this->openingCategoryForm = true;
    }

    public function saveCategory(): void
    {
        $this->resetErrorBag();

        if ($this->category->id !== null) {
            $this->authorize('update', $this->category);
        }

        $this->validate();

        $this->category->user_id = $this->user->id;

        if (!$this->category->save()) {
            $this->dispatchBrowserEvent('banner-message', [
                'style' => 'danger',
                'message' => 'Error'
            ]);

            $this->openingCategoryForm = false;

            return;
        }

        $this->emitSelf('category-saved');

        $this->openingCategoryForm = false;

        $this->dispatchBrowserEvent('banner-message', [
            'style' => 'success',
            'message' => 'Saved'
        ]);
    }

    public function confirmCategoryDeletion(Category $category): void
    {
        $this->category = $category;

        $this->confirmingCategoryDeletion = true;
    }

    public function deleteCategory(): void
    {
        $this->authorize('delete', $this->category);

        $categoryId = $this->category->id;

        if (!$this->category->delete()) {
            $this->dispatchBrowserEvent('banner-message', [
                'style' => 'danger',
                'message' => 'Error'
            ]);

            $this->confirmingCategoryDeletion = false;

            return;
        }

        $this->emitSelf('category-deleted');

        $this->confirmingCategoryDeletion = false;

        $this->dispatchBrowserEvent('category-deleted', ['id' => $categoryId]);

        $this->dispatchBrowserEvent('banner-message', [
            'style' => 'success',
            'message' => 'Deleted'
        ]);
    }

    public function confirmMassCategoryDeletion(): void
    {
        $this->massDeletion = true;

        $this->confirmingCategoryDeletion = true;
    }

    public function deleteCategories(): void
    {
        Category::destroy($this->selectedCategories);

        $this->emitSelf('categories-deleted');

        $this->confirmingCategoryDeletion = false;

        $this->massDeletion = false;

        $this->selectedCategories = [];

        $this->dispatchBrowserEvent('banner-message', [
            'style' => 'success',
            'message' => 'Deleted'
        ]);
    }

    public function render()
    {
        return view('livewire.category-list', [
            'categories' => $this->user
                ->categories()
                ->search($this->search)
                ->paginate($this->perPage)
        ]);
    }
}
