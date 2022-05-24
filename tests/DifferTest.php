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
        $assertMethod = $formatter === 'json' ? 'assertJsonStringEqualsJsonString' : 'assertEquals';
        $this->$assertMethod(
            $this->getExpected('expected' . ucfirst($formatter) . '.txt'),
            genDiff($this->makePath($file1), $this->makePath($file2), $formatter)
        );
    }

    public function additionProvider(): array
    {
        return [
            'yaml files' => ['file1.yaml', 'file2.yaml'],
            'json files' => ['file1.json', 'file2.json']
        ];
    }

    public function makePath(string $fileName): string
    {
        return __DIR__ . "/../tests/fixtures/{$fileName}";
    }

    public function getExpected(string $fileName): string
    {
        return trim(file_get_contents($this->makePath($fileName)));
    }
}
