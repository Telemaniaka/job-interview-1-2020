<?php

declare(strict_types=1);

namespace Recruitment\CommissionTask\Tests\Operation;

use PHPUnit\Framework\TestCase;
use Recruitment\CommissionTask\Operation\CashOut;
use Recruitment\CommissionTask\Service\OperationLog;

class CashOutTest extends TestCase
{
    protected $operationLog;
    protected $operation;
    protected $rates;

    protected function setUp()
    {
        $this->operationLog = new OperationLog();
        $this->rates = [
            'natural' => [
                'commissionRate' => 0.3,
                'dontTaxUnderPerWeek' => 1000,
            ],
            'legal' => [
                'commissionRate' => 0.3,
                'minCommission' => 0.5,
            ]
        ];
        $this->operation = new CashOut($this->operationLog);
    }

    /**
     * @dataProvider dataProviderForCalculateCommissionsTesting
     */
    public function testCalculateCommissions(array $data, string $expectation)
    {
        $this->operation->setCommissionRates($this->rates[$data[2]]);

        $this->assertEquals(
            $expectation,
            $this->operation->process(...$data)
        );
    }

    public function testMultipleCashOutsInTheSameWeek()
    {
        $testData = [
            [['2014-12-31', 4, 'natural', 1200.00, 'EUR'], '0.6'],
            [['2015-01-01', 4, 'natural', 1000.00, 'EUR'], '3'],
        ];

        foreach ($testData as $data) {
            $this->operation->setCommissionRates($this->rates[$data[0][2]]);

            $this->assertEquals(
                $data[1],
                $this->operation->process(...$data[0])
            );
        }
    }

    public function dataProviderForCalculateCommissionsTesting(): array
    {
        return [
            'over weekly max amount' => [['2014-12-31', 4, 'natural', 1200.00, 'EUR'], '0.60'],
            'at weekly max amount' => [['2016-01-05', 4, 'natural', 1000.00, 'EUR'], '0'],
            'other currency' => [['2016-01-06', 1, 'natural', 30000,'JPY'], '0'],
            'other huge currency' => [['2016-02-19', 5, 'natural', 3000000, 'JPY'], '8612'],
        ];
    }
}
