<?php

declare(strict_types=1);

namespace App\Enums;

enum TransactionType: int
{
    case Income = 1;
    case Expense = 2;

    public function colors(): string
    {
        return match ($this) {
            TransactionType::Income => 'bg-green-100 text-green-800',
            TransactionType::Expense => 'bg-red-100 text-red-800',
        };
    }
}
