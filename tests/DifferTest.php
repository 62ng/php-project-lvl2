<?php

namespace Differ\Phpunit\Tests;

use PHPUnit\Framework\TestCase;
use function Differ\genDiff;

class DifferTest extends TestCase
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

        $filePath1 = __DIR__ . '/../src/file1.json';
        $filePath2 = __DIR__ . '/../src/file2.json';

        $this->assertEquals($expected, genDiff($filePath1, $filePath2));
    }
}
