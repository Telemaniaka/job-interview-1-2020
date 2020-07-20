<?php

declare(strict_types=1);

namespace Recruitment\CommissionTask\Tests\Operation;

use PHPUnit\Framework\TestCase;
use Recruitment\CommissionTask\Operation\CashOut;
use Recruitment\CommissionTask\Service\OperationLog;

class CashOutTest extends TestCase
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
        $operation = new CashOut($this->operationLog);
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
            'over weekly max amount' => [['2014-12-31', '4', 'natural', 'cash_out', '1200.00', 'EUR'], '0.60'],
            'at weekly max amount' => [['2016-01-05', '4', 'natural', 'cash_out', '1000.00', 'EUR'], '0'],
            'other curency' => [['2016-01-06','1','natural','cash_out','30000','JPY'], '0'],
            'other huge curency' => [['2016-02-19','5','natural','cash_out','3000000','JPY'], '8612'],
        ];
    }
}
