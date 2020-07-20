<?php

declare(strict_types=1);

namespace Recruitment\CommissionTask\Service;

use Recruitment\CommissionTask\Interfaces\Output;

class StdOut implements Output
{
    public function print(string $message)
    {
        echo $message."\n";
    }
}
