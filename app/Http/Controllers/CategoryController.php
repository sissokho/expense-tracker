<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('categories.index');
    }
}
