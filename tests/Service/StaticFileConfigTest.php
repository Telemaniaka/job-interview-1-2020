<?php

declare(strict_types=1);

namespace Recruitment\CommissionTask\Tests\Service;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Recruitment\CommissionTask\Service\StaticFileConfig;

class StaticFileConfigTest extends TestCase
{

    private $file_system;

    public function setUp()
    {
        $directory = [
            'config.php' => '<?php
                return [
                    \'client\' => [
                        \'natural\' => [
                            \'cashIn\' => [
                                \'commissionRate\' => 0.03,
                                \'maxCommission\' => 5,
                            ],
                            \'cashOut\' => [
                                \'commissionRate\' => 0.03,
                                \'maxCommission\' => 5,
                                \'dontTaxUnderPerWeek\' => 1000,
                            ]
                        ],
                        \'legal\' => [
                            \'cashIn\' => [
                                \'commissionRate\' => 0.03,
                                \'maxCommission\' => 5,
                            ],
                            \'cashOut\' => [
                                \'commissionRate\' => 0.03,
                                \'minCommission\' => 0.5,
                            ]
                
                        ]
                    ]
                ];',
        ];
        // setup and cache the virtual file system
        $this->file_system = vfsStream::setup('root', 444, $directory);
    }

    public function testThrowExceptionOnNonExistingFile()
    {
        $this->expectExceptionMessage('Config File Not Found');
        new StaticFileConfig('NonExistingFile.php');
    }

    public function testConfigGetsInitialised()
    {
        $config = new StaticFileConfig($this->file_system->url() . '/config.php');
        $this->assertNotNull($config);
    }

    /**
     * @dataProvider dataProviderConfigArrayTesting
     */
    public function testConfigReturnsCorrectValueArray($configKey, $expectedValue)
    {
        $config = new StaticFileConfig($this->file_system->url() . '/config.php');

        $this->assertEquals($expectedValue, $config->getArray($configKey));
    }

    /**
     * @dataProvider dataProviderConfigSingleValueTesting
     */
    public function testConfigReturnsCorrectValueString($configKey, $expectedValue)
    {
        $config = new StaticFileConfig($this->file_system->url() . '/config.php');

        $this->assertEquals($expectedValue, $config->getSingleValue($configKey));
    }

    public function testThrowExceptionOnGettingArrayWithSingleValueMethod()
    {
        $config = new StaticFileConfig($this->file_system->url() . '/config.php');

        $configKey = 'client.legal.cashIn';
        $this->expectExceptionMessage('Config Variable '.$configKey.' is array');
        $config->getSingleValue($configKey);

    }


    public function dataProviderConfigArrayTesting(): array
    {
        return [
            'return operation commission rates' => ['client.natural.cashIn', ['commissionRate' => 0.03, 'maxCommission' => 5]],
            'return null on missing key' => ['client.fake.cashIn.maxCommission', []],
        ];
    }

    public function dataProviderConfigSingleValueTesting(): array
    {
        return [
            'return exact value'   => ['client.natural.cashIn.maxCommission', '5'],
            'return null on missing key' => ['client.fake.cashIn.maxCommission', ''],
        ];
    }

}
