<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\ValueObjects\Dollar;
use PHPUnit\Framework\TestCase;
use TypeError;

class DollarTest extends TestCase
{
    /** @test */
    public function can_be_constructed(): void
    {
        $this->assertInstanceOf(Dollar::class, new Dollar(1));
    }

    /** @test */
    public function can_be_constructed_from_cents(): void
    {
        $money = Dollar::fromCents(1000);

        $this->assertSame(10.0, $money->amount);
    }

    /** @test */
    public function when_given_null_it_converts_it_to_zero(): void
    {
        $money = Dollar::fromCents(null);

        $this->assertSame(0.0, $money->amount);
    }

    /** @test */
    public function fails_when_given_other_than_floats_or_null(): void
    {
        $this->expectException(TypeError::class);
        new Dollar('ff');
    }

    /** @test */
    public function can_check_if_amount_is_positive(): void
    {
        $this->assertTrue((new Dollar(10))->isPositive());

        $this->assertFalse((new Dollar(-10))->isPositive());
    }

    /** @test */
    public function can_convert_amount_to_cents(): void
    {
        $this->assertSame(15463, (new Dollar(154.63))->toCents());
        $this->assertSame(6799, (new Dollar(67.99))->toCents());
        $this->assertSame(16208, (new Dollar(162.08))->toCents());
    }

    /** @test */
    public function is_correctly_formatted_as_string(): void
    {
        $this->assertSame('$10.00', (new Dollar(10))->__toString());

        $this->assertSame('-$2.50', (string) new Dollar(-2.5));
    }
}
