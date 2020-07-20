<?php

declare(strict_types=1);

namespace Recruitment\CommissionTask;

use Recruitment\CommissionTask\Interfaces\Output;
use Recruitment\CommissionTask\Service\FileReader;
use Recruitment\CommissionTask\Service\OperationLog;

class App
{
    protected $inputFileName;
    protected $output;

    public function __construct(array $argv, Output $output)
    {
        if (empty($argv[1])) {
            $output->print('Missing input filename');
            die();
        }

        $this->inputFileName = $argv[1];
        $this->output = $output;
    }

    public function run()
    {
        try {
            $fileReader = new FileReader($this->inputFileName);
            $operationLog = new OperationLog();
            $processor = new Processor($fileReader, $this->output, $operationLog);
            $processor->process();
        } catch (\Exception $e) {
            $this->output->print('Error occured: '.$e->getMessage());
        }
    }
}
