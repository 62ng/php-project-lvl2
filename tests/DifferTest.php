<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testStylishGenDiff(): void
    {
        $expectedPath = __DIR__ . '/../tests/files/expectedStylish.txt';
        $expected = trim(file_get_contents($expectedPath));

        $filePathYaml1 = __DIR__ . '/../tests/files/file1.yaml';
        $filePathYaml2 = __DIR__ . '/../tests/files/file2.yaml';
        $this->assertEquals($expected, genDiff($filePathYaml1, $filePathYaml2));

        $filePathJson1 = __DIR__ . '/../tests/files/file1.json';
        $filePathJson2 = __DIR__ . '/../tests/files/file2.json';
        $this->assertEquals($expected, genDiff($filePathJson1, $filePathJson2));
    }

    public function testPlainGenDiff(): void
    {
        $expectedPath = __DIR__ . '/../tests/files/expectedPlain.txt';
        $expected = trim(file_get_contents($expectedPath));

        $filePathYaml1 = __DIR__ . '/../tests/files/file1.yaml';
        $filePathYaml2 = __DIR__ . '/../tests/files/file2.yaml';
        $this->assertEquals($expected, genDiff($filePathYaml1, $filePathYaml2, 'plain'));

        $filePathJson1 = __DIR__ . '/../tests/files/file1.json';
        $filePathJson2 = __DIR__ . '/../tests/files/file2.json';
        $this->assertEquals($expected, genDiff($filePathJson1, $filePathJson2, 'plain'));
    }

    public function testJsonGenDiff(): void
    {
        $expectedPath = __DIR__ . '/../tests/files/expectedJson.txt';
        $expected = trim(file_get_contents($expectedPath));

        $filePathYaml1 = __DIR__ . '/../tests/files/file1.yaml';
        $filePathYaml2 = __DIR__ . '/../tests/files/file2.yaml';
        $this->assertJsonStringEqualsJsonString($expected, genDiff($filePathYaml1, $filePathYaml2, 'json'));

        $filePathJson1 = __DIR__ . '/../tests/files/file1.json';
        $filePathJson2 = __DIR__ . '/../tests/files/file2.json';
        $this->assertJsonStringEqualsJsonString($expected, genDiff($filePathJson1, $filePathJson2, 'json'));
    }
}
