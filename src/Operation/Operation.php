<?php

declare(strict_types=1);

namespace Recruitment\CommissionTask\Operation;

use Recruitment\CommissionTask\Service\OperationLog;

class Operation
{
    protected $date;
    protected $userId;
    protected $userType;
    protected $amount;
    protected $currency;
    protected $operationLog;

    protected $commissionRate = 0;

    public static $supportedOperations = [
        'cashOut' => CashOut::class,
        'cashIn' => CashIn::class,
    ];

    public function __construct(OperationLog $log)
    {
        $this->operationLog = $log;
    }

    public function setCommissionRates(array $rates)
    {
        foreach ($rates as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    public function process(string $date, int $userId, string $userType, float $amount, string $currency): float
    {
        $this->date = $date;
        $this->userId = $userId;
        $this->userType = $userType;
        $this->userId = $userId;
        $this->amount = $amount;
        $this->currency = strtoupper($currency);

        return $this->calculateCommission();
    }

    public function calculateCommission(): float
    {
        return $this->amount * ($this->commissionRate / 100);
    }
}
