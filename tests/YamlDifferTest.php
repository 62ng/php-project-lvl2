<?php

namespace YamlDiffer\Phpunit\Tests;

use PHPUnit\Framework\TestCase;
use function YamlDiffer\genDiff;
use function YDiffer\Parsers\parseFile;

class YamlDifferTest extends TestCase
{
    public function testGenDiff(): void
    {
        $expected = "{
\t- follow: false
\t  host: hexlet.io
\t- proxy: 123.234.53.22
\t- timeout: 50
\t+ timeout: 20
\t+ verbose: true
}\n";

        $filePath1 = __DIR__ . '/../src/files/file1.yaml';
        $filePath2 = __DIR__ . '/../src/files/file2.yaml';

        $this->assertEquals($expected, genDiff($filePath1, $filePath2));
    }
}
