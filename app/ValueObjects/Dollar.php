<?php

declare(strict_types=1);

namespace App\ValueObjects;

use InvalidArgumentException;
use NumberFormatter;

final class Dollar
{
    public function __construct(private float $amount)
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('The amount must be greater than 0.');
        }
    }

    public function fromCents(int $amount): self
    {
        return new self((float) $amount / 100);
    }

    public function toCents(): int
    {
        return (int) $this->amount * 100;
    }

    public function __toString(): string
    {
        $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 0);

        return (string) $formatter->formatCurrency($this->amount, 'USD');
    }
}
