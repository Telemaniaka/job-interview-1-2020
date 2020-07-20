<?php

declare(strict_types=1);

namespace Recruitment\CommissionTask\Service;

class OperationLog
{
    private $log;

    public function record(int $userId, string $date, float $amount)
    {
        if (!isset($this->log[$userId])) {
            $this->log[$userId] = [];
        }

        $week = $this->getWeekStart($date);

        if (!isset($this->log[$userId][$week])) {
            $this->log[$userId][$week] = [];
        }

        $this->log[$userId][$week][] = $amount;
    }

    public function getWeeklyTransactions($userId, $date)
    {
        $week = $this->getWeekStart($date);

        return $this->log[$userId][$week] ?? [];
    }

    public function getWeekStart(string $date)
    {
        $date = new \DateTime($date);
        $weekday = $date->format('N');
        $date->modify('-'.$weekday.' day'); // Edit

        return $date->format('Y-m-d');
    }
}
