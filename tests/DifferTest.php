<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     */
    public function testStylishGenDiff(string $file1, string $file2): void
    {
        $this->assertEquals(
            $this->getExpected('expectedStylish.txt'),
            genDiff($this->makePath($file1), $this->makePath($file2))
        );
    }

    /**
     * @dataProvider additionProvider
     */
    public function testPlainGenDiff(string $file1, string $file2): void
    {
        $this->assertEquals(
            $this->getExpected('expectedPlain.txt'),
            genDiff($this->makePath($file1), $this->makePath($file2), 'plain')
        );
    }

    /**
     * @dataProvider additionProvider
     */
    public function testJsonGenDiff(string $file1, string $file2): void
    {
        $this->assertJsonStringEqualsJsonString(
            $this->getExpected('expectedJson.txt'),
            genDiff($this->makePath($file1), $this->makePath($file2), 'json')
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
