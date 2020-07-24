<?php

declare(strict_types=1);

namespace Recruitment\CommissionTask\Operation;

use Recruitment\CommissionTask\Currency\Currency;

class CashIn extends Operation
{
    protected $maxCommission;

    public function calculateCommission(): float
    {
        $commission = $this->amount * ($this->commissionRate / 100);
        $maxCommission = Currency::convert(Currency::$defaultCurency, $this->maxCommission, $this->currency);

        if ($commission > $maxCommission) {
            $commission = $maxCommission;
        }

        return $commission;
    }
}
