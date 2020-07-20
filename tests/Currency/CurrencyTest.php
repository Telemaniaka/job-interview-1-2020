<?php

declare(strict_types=1);

namespace Recruitment\CommissionTask\Tests\Currency;

use PHPUnit\Framework\TestCase;
use Recruitment\CommissionTask\Currency\Currency;

class CurrencyTest extends TestCase
{
    /**
     * @dataProvider dataProviderForConversionTesting
     */
    public function testCalculateConversions(string $from, float $ammount, string $to, float $expectation)
    {
        $this->assertEquals(
            $expectation,
            Currency::convert($from, $ammount, $to)
        );
    }

    public function dataProviderForConversionTesting(): array
    {
        return [
            ["EUR", 1 , "EUR", 1],
            ["USD", 1 , "USD", 1],
            ["JPY", 1 , "JPY", 1],
            ["EUR", 1 , "USD", 1.15],
            ["USD", 1 , "JPY", 113],
            ["JPY", 1 , "EUR", 0.01],
        ];
    }
}
