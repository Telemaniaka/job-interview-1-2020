<?php

declare(strict_types=1);

namespace Recruitment\CommissionTask\Operation;

use Recruitment\CommissionTask\Service\OperationLog;

class Operation
{
    protected $data;
    protected $commission = 0;
    protected $commissionRate = 0;
    protected $date;
    protected $amount;
    protected $currency;
    protected $operationLog;

    public static $supprtedOperations = [
        'cash_out' => CashOut::class,
        'cash_in' => CashIn::class,
    ];

    public function __construct(OperationLog $log)
    {
        $this->operationLog = $log;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function process()
    {
        $this->calculateCommision();
    }

    public function calculateCommision()
    {
        $this->commission = $this->amount * ($this->commissionRate / 100);
    }

    public function getCommission()
    {
        return $this->commission;
    }
}
