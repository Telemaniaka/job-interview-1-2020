<?php

declare(strict_types=1);

namespace Recruitment\CommissionTask\Tests\Service;

use PHPUnit\Framework\TestCase;
use Recruitment\CommissionTask\Service\Math;

class MathTest extends TestCase
{
    /**
     * @var Math
     */
    private $math;

    public function setUp()
    {
        $this->math = new Math(2);
    }

    /**
     * @param string $leftOperand
     * @param string $rightOperand
     * @param string $expectation
     *
     * @dataProvider dataProviderForAddTesting
     */
    public function testAdd(string $leftOperand, string $rightOperand, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->math->add($leftOperand, $rightOperand)
        );
    }

    /**
     * @param string $amount
     * @param string $expectation
     *
     * @dataProvider dataProviderForCeilTesting
     */
    public function testPrecisionCeil(string $amount, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            Math::precisionCeil($amount)
        );
    }


    public function dataProviderForAddTesting(): array
    {
        return [
            'add 2 natural numbers' => ['1', '2', '3'],
            'add negative number to a positive' => ['-1', '2', '1'],
            'add natural number to a float' => ['1', '1.05123', '2.05'],
        ];
    }

    public function dataProviderForCeilTesting(): array
    {
        return [
            ['1', '1.00'],
            ['1.4', '1.40'],
            ['0.023', '0.03'],
        ];
    }
}
