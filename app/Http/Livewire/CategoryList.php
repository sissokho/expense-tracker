<?php

namespace App\Http\Livewire;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryList extends Component
{
    use WithPagination;

    public $perPage = 10;

    public function render()
    {
        return view('livewire.category-list', [
            'categories' => Category::query()
                ->paginate($this->perPage)
        ]);
    }
}
