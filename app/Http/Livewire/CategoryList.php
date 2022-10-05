<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class CategoryList extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => '']
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function getUserProperty()
    {
        return auth()->user();
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
