<?php

namespace App\Http\Livewire;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryList extends Component
{
    public function render()
    {
        return view('livewire.category-list', [
            'categories' => Category::all()
        ]);
    }
}
