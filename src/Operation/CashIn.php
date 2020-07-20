<?php

declare(strict_types=1);

namespace Recruitment\CommissionTask\Operation;

use Recruitment\CommissionTask\Currency\Currency;

class CashIn extends Operation
{
    protected $commissionRate = 0.03;
    protected $maxCommission = 5;
    protected $user;

    public function setData(array $data)
    {
        $this->date = $data[0];
        $this->amount = floatval($data[4]);
        $this->currency = strtoupper($data[5]);
    }

    public function calculateCommision()
    {
        $this->commission = $this->amount * ($this->commissionRate / 100);
        $maxCommission = Currency::convert(Currency::$defaultCurency, $this->maxCommission, $this->currency);

        if ($this->commission > $maxCommission) {
            $this->commission = $maxCommission;
        }
    }
}
