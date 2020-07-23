<?php

declare(strict_types=1);

namespace Recruitment\CommissionTask;

use Recruitment\CommissionTask\Interfaces\Config;
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
    protected $config;

    public function __construct(FileReader $file, Output $output, OperationLog $log, Config $config)
    {
        $this->file = $file;
        $this->output = $output;
        $this->operationLog = $log;
        $this->config = $config;
    }

    public function process(int $limit = 0)
    {
        while ($line = $this->file->getLine()) {
            $operationData = explode(',', $line);
            $operationName = lcfirst(str_replace('_', '', ucwords($operationData[3], '_')));

            if (!in_array($operationName, array_keys(Operation::$supportedOperations), true)) {
                $this->output->print('Unsupported operation:'.$operationData[3]);
                continue;
            }

            $operation = new Operation::$supportedOperations[$operationName]($this->operationLog);
            $operation->setCommissionRates($this->config->getArray(
                'client.'.$operationData[2].'.'.$operationName
            ));
            $operation->setData($operationData);
            $operation->process();
            $commission = $operation->getCommission();
            $this->output->print((string) $commission);
        }
    }
}
