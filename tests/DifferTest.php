<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testStylishGenDiff(): void
    {
        $expected = "{
    common: {
      + follow: false
        setting1: Value 1
      - setting2: 200
      - setting3: true
      + setting3: null
      + setting4: blah blah
      + setting5: {
            key5: value5
        }
        setting6: {
            doge: {
              - wow: 
              + wow: so much
            }
            key: value
          + ops: vops
        }
    }
    group1: {
      - baz: bas
      + baz: bars
        foo: bar
      - nest: {
            key: value
        }
      + nest: str
    }
  - group2: {
        abc: 12345
        deep: {
            id: 45
        }
    }
  + group3: {
        deep: {
            id: {
                number: 45
            }
        }
        fee: 100500
    }
}";

        $filePathYaml1 = __DIR__ . '/../src/files/file11.yaml';
        $filePathYaml2 = __DIR__ . '/../src/files/file22.yaml';
        $this->assertEquals($expected, genDiff($filePathYaml1, $filePathYaml2, 'stylish'));

        $filePathJson1 = __DIR__ . '/../src/files/file11.json';
        $filePathJson2 = __DIR__ . '/../src/files/file22.json';
        $this->assertEquals($expected, genDiff($filePathJson1, $filePathJson2, 'stylish'));
    }

    public function testPlainGenDiff(): void
    {
        $expected = "Property 'common.follow' was added with value: false
Property 'common.setting2' was removed
Property 'common.setting3' was updated. From true to null
Property 'common.setting4' was added with value: 'blah blah'
Property 'common.setting5' was added with value: [complex value]
Property 'common.setting6.doge.wow' was updated. From '' to 'so much'
Property 'common.setting6.ops' was added with value: 'vops'
Property 'group1.baz' was updated. From 'bas' to 'bars'
Property 'group1.nest' was updated. From [complex value] to 'str'
Property 'group2' was removed
Property 'group3' was added with value: [complex value]";

        $filePathYaml1 = __DIR__ . '/../src/files/file11.yaml';
        $filePathYaml2 = __DIR__ . '/../src/files/file22.yaml';
        $this->assertEquals($expected, genDiff($filePathYaml1, $filePathYaml2, 'plain'));

        $filePathJson1 = __DIR__ . '/../src/files/file11.json';
        $filePathJson2 = __DIR__ . '/../src/files/file22.json';
        $this->assertEquals($expected, genDiff($filePathJson1, $filePathJson2, 'plain'));
    }

    public function testJsonGenDiff(): void
    {
        $expectedJsonPath = __DIR__ . '/../tests/files/expectedJson.json';

        $filePathYaml1 = __DIR__ . '/../src/files/file11.yaml';
        $filePathYaml2 = __DIR__ . '/../src/files/file22.yaml';
        $this->assertJsonStringEqualsJsonString(file_get_contents($expectedJsonPath), genDiff($filePathYaml1, $filePathYaml2, 'json'));

        $filePathJson1 = __DIR__ . '/../src/files/file11.json';
        $filePathJson2 = __DIR__ . '/../src/files/file22.json';
        $this->assertJsonStringEqualsJsonString(file_get_contents($expectedJsonPath), genDiff($filePathJson1, $filePathJson2, 'json'));
    }
}
