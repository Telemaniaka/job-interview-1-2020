<?php

declare(strict_types=1);

namespace Recruitment\CommissionTask\Service;

use Recruitment\CommissionTask\Interfaces\Config;

class StaticFileConfig implements Config
{
    private $configArray = [];

    public function __construct(string $filename)
    {
        if (!file_exists($filename)) {
            throw new \Exception('Config File Not Found');
        }
        // open file handler
        $this->configArray = include $filename;
    }

    public function getSingleValue(string $keyName): string
    {
        $keyNames = explode('.', $keyName);
        $configValue = $this->configArray;
        foreach ($keyNames as $key) {
            if (!isset($configValue[$key])) {
                return '';
            }
            $configValue = $configValue[$key];
        }

        if (is_array($configValue)) {
            throw new \Exception('Config Variable '.$keyName.' is array');
        }

        return (string) $configValue;
    }

    public function getArray(string $keyName): array
    {
        $keyNames = explode('.', $keyName);
        $configValue = $this->configArray;
        foreach ($keyNames as $key) {
            if (!isset($configValue[$key])) {
                return [];
            }
            $configValue = $configValue[$key];
        }

        return $configValue;
    }
}
