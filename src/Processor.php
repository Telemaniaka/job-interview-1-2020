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
            if ($limit > 0 && $this->processedItemCount > $limit) {
                break;
            }
            $operationData = $this->transformInputToAssocArray($line);

            if (!in_array($operationData['operation'], array_keys(Operation::$supportedOperations), true)) {
                $this->output->print('Unsupported operation:'.$operationData['operation']);
                continue;
            }

            $operation = new Operation::$supportedOperations[$operationData['operation']]($this->operationLog);
            $operation->setCommissionRates($this->config->getArray(
                'client.'.$operationData['userType'].'.'.$operationData['operation']
            ));
            $commission = $operation->process(
                $operationData['date'],
                (int) $operationData['userId'],
                $operationData['userType'],
                floatval($operationData['amount']),
                $operationData['currency']
            );
            $this->output->print(strval($commission));
            ++$this->processedItemCount;
        }
    }

    protected function transformInputToAssocArray($line)
    {
        $data = explode(',', $line);

        return [
            'date' => $data[0],
            'userId' => $data[1],
            'userType' => $data[2],
            'operation' => lcfirst(str_replace('_', '', ucwords($data[3], '_'))),
            'amount' => $data[4],
            'currency' => $data[5],
        ];
    }
}
