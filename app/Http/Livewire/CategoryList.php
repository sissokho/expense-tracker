<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * @property-read User $user
 */
class CategoryList extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;
    public bool $openingCategoryForm = false;
    public ?Category $category = null;

    protected $queryString = [
        'search' => ['except' => '']
    ];

    protected $rules = [
        'category.name' => ['required', 'string', 'max:255']
    ];

    protected $listeners = [
        'category-saved' => '$refresh'
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
