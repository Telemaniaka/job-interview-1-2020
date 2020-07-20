<?php

declare(strict_types=1);

namespace Recruitment\CommissionTask\Service;

class Math
{
    private $scale;

    public function __construct(int $scale)
    {
        $this->scale = $scale;
    }

    public function add(string $leftOperand, string $rightOperand): string
    {
        return bcadd($leftOperand, $rightOperand, $this->scale);
    }

    public static function precisionCeil($value, $precision = 2): float
    {
        $offset = 0.5;
        if ($precision !== 0) {
            $offset /= pow(10, $precision);
        }
        $final = round($value + $offset, $precision, PHP_ROUND_HALF_DOWN);

        return $final === -0 ? 0 : $final;
    }
}
