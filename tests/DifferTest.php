<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     */
    public function testGenDiff(string $file1, string $file2, string $expectedFile, string $formatter = 'stylish'): void
    {
        $assertMethod = ($formatter === 'json') ? 'assertJsonStringEqualsJsonFile' : 'assertStringEqualsFile';

        $this->$assertMethod(
            $this->makePathToFixture($expectedFile),
            genDiff($this->makePathToFixture($file1), $this->makePathToFixture($file2), $formatter)
        );
    }

    public function additionProvider(): array
    {
        return [
            'yaml files to stylish' => ['file1.yaml', 'file2.yaml', 'expectedStylish.txt'],
            'json files to stylish' => ['file1.json', 'file2.json', 'expectedStylish.txt'],
            'yaml files to plain' => ['file1.yaml', 'file2.yaml', 'expectedPlain.txt', 'plain'],
            'json files to plain' => ['file1.json', 'file2.json', 'expectedPlain.txt', 'plain'],
            'yaml files to json' => ['file1.yaml', 'file2.yaml', 'expectedJson.txt', 'json'],
            'json files to json' => ['file1.json', 'file2.json', 'expectedJson.txt', 'json']
        ];
    }

    public function makePathToFixture(string $fileName): string
    {
        $parts = [__DIR__, 'fixtures', $fileName];
        return realpath(implode('/', $parts));
    }
}
