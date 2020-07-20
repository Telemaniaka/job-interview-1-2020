<?php

declare(strict_types=1);

namespace Recruitment\CommissionTask;

use Recruitment\CommissionTask\Interfaces\Output;
use Recruitment\CommissionTask\Operation\Operation;
use Recruitment\CommissionTask\Service\FileReader;
use Recruitment\CommissionTask\Service\OperationLog;

class Processor
{
    protected $file;
    protected $output;
    protected $processedItemCount;
    protected $operationLog;

    public function __construct(FileReader $file, Output $output, OperationLog $log)
    {
        $this->file = $file;
        $this->output = $output;
        $this->operationLog = $log;
    }

    public function process(int $limit = 0)
    {
        while ($line = $this->file->getLine()) {
            $operation_data = explode(',', $line);

            if (!in_array($operation_data[3], array_keys(Operation::$supprtedOperations), true)) {
                $this->output->print('Unsupported operation:'.$operation_data[3]);
                continue;
            }

            $operation = new Operation::$supprtedOperations[$operation_data[3]]($this->operationLog);
            $operation->setData($operation_data);
            $operation->process();
            $commission = $operation->getCommission();
            $this->output->print((string) $commission);
        }
    }
}
