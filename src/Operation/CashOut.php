<?php

declare(strict_types=1);

namespace Recruitment\CommissionTask\Operation;

use Recruitment\CommissionTask\Currency\Currency;

class CashOut extends Operation
{
    protected $commissionRate = [
        'legal' => 0.3,
        'natural' => 0.3,
    ];

    protected $maxCommission = [
        'natural' => 5,
    ];

    protected $minCommission = [
        'legal' => 0.50,
    ];

    protected $dontTaxUnderPerWeek = 1000;

    protected $user;
    protected $userType;

    public function setData(array $data)
    {
        $this->date = $data[0];
        $this->user = $data[1];
        $this->userType = $data[2];
        $this->amount = floatval($data[4]);
        $this->currency = strtoupper($data[5]);
    }

    public function process()
    {
        parent::process();
        $this->operationLog->record(
            (int) $this->user,
            $this->date,
            Currency::convert(
                $this->currency,
                $this->amount,
                Currency::$defaultCurency
            ));
    }

    public function calculateCommision()
    {
        if ($this->userType === 'legal') {
            $this->commission = $this->calculateCommissionLegal();

            return;
        }
        if ($this->userType === 'natural') {
            $this->commission = $this->calculateCommissionNatural();

            return;
        }
    }

    public function calculateCommissionLegal(): float
    {
        $commission = $this->amount * ($this->commissionRate['legal'] / 100);

        $minCommision = Currency::convert(
            Currency::$defaultCurency,
            $this->minCommission['legal'],
            $this->currency
        );

        if ($commission < $minCommision) {
            $commission = $minCommision;
        }

        return $commission;
    }

    public function calculateCommissionNatural(): float
    {
        $weeklyCashOuts = $this->operationLog->getWeeklyTransactions($this->user, $this->date);

        $taxableAmmount = $this->amount;
        $convertedAmmount = floatval(Currency::convert(
            $this->currency,
            $this->amount,
            Currency::$defaultCurency
        ));

        $weeklySum = array_sum($weeklyCashOuts);
        if ($weeklySum + $convertedAmmount <= $this->dontTaxUnderPerWeek) {
            if (count($weeklyCashOuts) <= 3) {
                return 0;
            }
        }

        if ($weeklySum <= $this->dontTaxUnderPerWeek) {
            $taxableAmmount =
                Currency::convert(
                    Currency::$defaultCurency,
                    ($weeklySum + $convertedAmmount) - $this->dontTaxUnderPerWeek,
                    $this->currency
                );
        }

        // not ideal but it's the end of the day. it should work
        $commission = Currency::convert(
            $this->currency,
            $taxableAmmount * ($this->commissionRate['natural'] / 100),
            $this->currency
        );

        return $commission;
    }
}
