<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\ValueObjects\Money;
use PHPUnit\Framework\TestCase;
use TypeError;

class MoneyTest extends TestCase
{
    /** @test */
    public function can_be_constructed(): void
    {
        $this->assertInstanceOf(Money::class, new Money(1));
    }

    /** @test */
    public function can_be_constructed_from_cents(): void
    {
        $money = Money::fromCents(1000);

        $this->assertSame(10.0, $money->amount);
    }

    /** @test */
    public function fails_when_given_other_than_floats(): void
    {
        $this->expectException(TypeError::class);
        new Money('ff');
    }

    /** @test */
    public function can_check_if_amount_is_positive(): void
    {
        $this->assertTrue((new Money(10))->isPositive());

        $this->assertFalse((new Money(-10))->isPositive());
    }

    /** @test */
    public function can_convert_amount_to_cents(): void
    {
        $this->assertSame(15463, (new Money(154.63))->toCents());
        $this->assertSame(6799, (new Money(67.99))->toCents());
        $this->assertSame(16208, (new Money(162.08))->toCents());
    }

    /** @test */
    public function is_correctly_formatted_as_string(): void
    {
        $this->assertSame('$10.00', (new Money(10))->__toString());

        $this->assertSame('-$2.50', (string) new Money(-2.5));
    }
}
