<?php

declare(strict_types=1);

namespace Recruitment\CommissionTask\Operation;

use Recruitment\CommissionTask\Currency\Currency;

class CashOut extends Operation
{
    protected $minCommission;
    protected $dontTaxUnderPerWeek;

    public function process(string $date, int $userId, string $userType, float $amount, string $currency): float
    {
        $return = parent::process($date, $userId, $userType, $amount, $currency);
        $this->operationLog->record(
            $this->userId,
            $this->date,
            Currency::convert(
                $this->currency,
                $this->amount,
                Currency::$defaultCurency
            )
        );

        return $return;
    }

    public function calculateCommission(): float
    {
        if ($this->userType === 'legal') {
            return $this->calculateCommissionLegal();
        }
        if ($this->userType === 'natural') {
            return $this->calculateCommissionNatural();
        }
    }

    public function calculateCommissionLegal(): float
    {
        $commission = $this->amount * ($this->commissionRate / 100);

        $minCommision = Currency::convert(
            Currency::$defaultCurency,
            $this->minCommission,
            $this->currency
        );

        if ($commission < $minCommision) {
            $commission = $minCommision;
        }

        return $commission;
    }

    public function calculateCommissionNatural(): float
    {
        $weeklyCashOuts = $this->operationLog->getWeeklyTransactions($this->userId, $this->date);
        $taxableAmount = $this->amount;
        $convertedAmount = floatval(Currency::convert(
            $this->currency,
            $this->amount,
            Currency::$defaultCurency
        ));

        $weeklySum = array_sum($weeklyCashOuts);
        if ($weeklySum + $convertedAmount <= $this->dontTaxUnderPerWeek) {
            if (count($weeklyCashOuts) <= 3) {
                return 0;
            }
        }

        if ($weeklySum <= $this->dontTaxUnderPerWeek) {
            $taxableAmount =
                Currency::convert(
                    Currency::$defaultCurency,
                    ($weeklySum + $convertedAmount) - $this->dontTaxUnderPerWeek,
                    $this->currency
                );
        }

        // not ideal but it's the end of the day. it should work
        return Currency::convert(
            $this->currency,
            $taxableAmount * ($this->commissionRate / 100),
            $this->currency
        );
    }
}
