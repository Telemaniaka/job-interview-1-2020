<?php

declare(strict_types=1);

namespace Recruitment\CommissionTask\Currency;

use Recruitment\CommissionTask\Service\Math;

class Currency
{
    public static $defaultCurency = 'EUR';
    public static $supportedCurencies = [
        'EUR' => Eur::class,
        'USD' => Usd::class,
        'JPY' => Jpy::class,
    ];

    public static function convert(string $from, float $amount, string $to): float
    {
        if (!in_array($from, array_keys(self::$supportedCurencies), true)) {
            throw new \Exception('Unsupported currency:'.$from);
        }

        if (!in_array($to, array_keys(self::$supportedCurencies), true)) {
            throw new \Exception('Unsupported currency:'.$to);
        }

        $toEuro = $amount / self::$supportedCurencies[$from]::$euroRate;
        $convertedAmount = $toEuro * self::$supportedCurencies[$to]::$euroRate;

        return Math::precisionCeil($convertedAmount, self::$supportedCurencies[$to]::$precision);
    }
}
