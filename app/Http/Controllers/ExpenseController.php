<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function __invoke(): View
    {
        return view('transactions', [
            'type' => TransactionType::Expense
        ]);
    }
}
