<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     */
    public function testGenDiff(string $file1, string $file2, string $formatter = 'stylish'): void
    {
        $assertMethod = $formatter === 'json' ? 'assertJsonStringEqualsJsonFile' : 'assertStringEqualsFile';
        $this->$assertMethod(
            $this->makePath('expected' . ucfirst($formatter) . '.txt'),
            genDiff($this->makePath($file1), $this->makePath($file2), $formatter)
        );
    }

    public function additionProvider(): array
    {
        return [
            'yaml files' => ['file1.yaml', 'file2.yaml'],
            'json files' => ['file1.json', 'file2.json'],
            'yaml files to plain' => ['file1.yaml', 'file2.yaml', 'plain'],
            'json files to plain' => ['file1.json', 'file2.json', 'plain'],
            'yaml files to json' => ['file1.yaml', 'file2.yaml', 'json'],
            'json files to json' => ['file1.json', 'file2.json', 'json']
        ];
    }

    public function makePath(string $fileName): string
    {
        $parts = [__DIR__, 'fixtures', $fileName];
        return realpath(implode('/', $parts));
    }
}
