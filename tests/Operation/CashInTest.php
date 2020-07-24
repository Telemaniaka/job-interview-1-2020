<?php

declare(strict_types=1);

namespace Recruitment\CommissionTask\Tests\Operation;

use PHPUnit\Framework\TestCase;
use Recruitment\CommissionTask\Operation\CashIn;
use Recruitment\CommissionTask\Service\OperationLog;

class CashInTest extends TestCase
{
    protected $operationLog;
    protected $rates;
    protected $operation;

    protected function setUp()
    {
        $this->operationLog = new OperationLog();
        $this->rates = [
            'natural' => [
                'commissionRate' => 0.03,
                'maxCommission' => 5,
            ],
            'legal' => [
                'commissionRate' => 0.03,
                'maxCommission' => 5,
            ]
        ];
        $this->operation = new CashIn($this->operationLog);
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

    public function dataProviderForCalculateCommissionsTesting(): array
    {
        return [
            'normal amount' => [['2016-01-05', 1, 'natural', 200.00, 'EUR'], '0.06'],
            'huge amount'   => [['2016-01-05', 1, 'natural', 1000000.00, 'EUR'], '5.00'],
            'other huge currency' => [['2016-01-05', 1, 'natural', 1000000.00, 'USD'], '5.75'],
        ];
    }
}
