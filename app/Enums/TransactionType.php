<?php

declare(strict_types=1);

namespace App\Enums;

enum TransactionType: int
{
    case Income = 1;
    case Expense = 2;
}
