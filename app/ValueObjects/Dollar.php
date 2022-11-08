<?php

declare(strict_types=1);

namespace App\ValueObjects;

use NumberFormatter;

final class Dollar
{
    public function __construct(public readonly float $amount)
    {
    }

    public static function fromCents(?int $amount): self
    {
        return new self($amount / 100);
    }

    public function toCents(): int
    {
        return (int) round($this->amount * 100);
    }

    public function isPositive(): bool
    {
        return $this->amount > 0;
    }

    public function __toString(): string
    {
        $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);

        return (string) $formatter->formatCurrency($this->amount, 'USD');
    }
}
