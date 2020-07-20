<?php

declare(strict_types=1);

namespace Recruitment\CommissionTask\Tests\Service;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Recruitment\CommissionTask\Service\FileReader;

class FileReaderTest extends TestCase
{

    private $file_system;

    public function setUp()
    {
        $directory = [
            'ExistingFile.csv'    => '',
            'FileWithContent.csv' => "Line1\nLine2\nLine3\nLine4",
        ];
        // setup and cache the virtual file system
        $this->file_system = vfsStream::setup('root', 444, $directory);
    }

    public function testThrowExceptionOnNonExistingFile()
    {
        $this->expectExceptionMessage('File Not Found');
        new FileReader('NonExistingFile.csv');
    }

    public function testFileReaderGetsInitialised()
    {
        $fileReader = new FileReader($this->file_system->url() . '/ExistingFile.csv');
        $this->assertNotNull($fileReader);
    }

    public function testFileReaderReturnsRowsOneByOne()
    {
        $fileReader = new FileReader($this->file_system->url() . '/FileWithContent.csv');

        $this->assertEquals('Line1', $fileReader->getLine());
        $this->assertEquals('Line2', $fileReader->getLine());
        $this->assertEquals('Line3', $fileReader->getLine());
        $this->assertEquals('Line4', $fileReader->getLine());
    }

}
