<?php

declare(strict_types=1);

namespace Recruitment\CommissionTask\Tests\Operation;

use PHPUnit\Framework\TestCase;
use Recruitment\CommissionTask\Operation\CashIn;
use Recruitment\CommissionTask\Service\OperationLog;

class CashInTest extends TestCase
{
    protected $operationLog;

    protected function setUp()
    {
        $this->operationLog = new OperationLog();
    }

    /**
     * @dataProvider dataProviderForCalculateCommissionsTesting
     */
    public function testCalculateCommissions(array $data, string $expectation)
    {
        $operation = new CashIn($this->operationLog);
        $operation->setData($data);
        $operation->calculateCommision();

        $this->assertEquals(
            $expectation,
            $operation->getCommission()
        );
    }

    public function dataProviderForCalculateCommissionsTesting(): array
    {
        return [
            'normal amount' => [['2016-01-05', '1', 'natural', 'cash_in', '200.00', 'EUR'], '0.06'],
            'huge amount'   => [['2016-01-05', '1', 'natural', 'cash_in', '1000000.00', 'EUR'], '5.00'],
            'other huge currency' => [['2016-01-05', '1', 'natural', 'cash_in', '1000000.00', 'USD'], '5.75'],
        ];
    }
}
