<?php

declare(strict_types=1);

namespace Recruitment\CommissionTask\Service;

class FileReader
{
    private $file;

    public function __construct(string $filename)
    {
        // if file !exists throw exception
        if (!file_exists($filename)) {
            throw new \Exception('File Not Found');
        }
        // open file handler
        $this->file = fopen($filename, 'r');
    }

    public function __destruct()
    {
        fclose($this->file);
    }

    public function getLine(): string
    {
        return str_replace(["\r", "\n"], '', fgets($this->file));
    }
}
