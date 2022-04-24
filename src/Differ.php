<?php

namespace Differ;

use Docopt;

function runDiff()
{
    $doc = <<<DOC

Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)
  gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  -v --version                  Show version
  --format <fmt>                Report format [default: stylish]
DOC;

    $args = Docopt::handle($doc, array('version'=>'Gendiff 1.0'));
    foreach ($args as $k=>$v)
        echo $k.': '.json_encode($v).PHP_EOL;
}

function getFileData($filePath): array
{
    if (!file_exists($filePath)) {
        return [];
    }

    $json = file_get_contents($filePath);

    return json_decode($json, true);
}

function genDiff(string $filePath1, string $filePath2): string
{
    $data1 = getFileData($filePath1);
    $data2 = getFileData($filePath2);

    $keys = array_merge($data1, $data2);
    ksort($keys);

    $diffs = "{\n";
    foreach ($keys as $key => $value) {
        if (is_bool($value)) {
            $value = ($value === true) ? 'true' : 'false';
        }
        if (!key_exists($key, $data2)) {
            $diffs .= "\t- {$key}: {$value}\n";
            continue;
        }
        if (!key_exists($key, $data1)) {
            $diffs .= "\t+ {$key}: {$value}\n";
            continue;
        }
        if ($data1[$key] === $data2[$key]) {
            $diffs .= "\t  {$key}: {$value}\n";
            continue;
        }
        $diffs .= "\t- {$key}: {$data1[$key]}\n";
        $diffs .= "\t+ {$key}: {$value}\n";
    }
    $diffs .= "}\n";

    return $diffs;
}