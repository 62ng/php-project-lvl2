<?php

namespace Differ;

use function Parsers\parseFile;

function genDiff(string $filePath1, string $filePath2): string
{
    $data1 = parseFile($filePath1);
    $data2 = parseFile($filePath2);

    $keys = array_merge($data1, $data2);
    ksort($keys);

    $diffs = ['{'];
    foreach ($keys as $key => $value) {
        if (is_bool($value)) {
            $value = ($value === true) ? 'true' : 'false';
        }
        if (!key_exists($key, $data2)) {
            $diffs[] = "\t- {$key}: {$value}";
            continue;
        }
        if (!key_exists($key, $data1)) {
            $diffs[] = "\t+ {$key}: {$value}";
            continue;
        }
        if ($data1[$key] === $data2[$key]) {
            $diffs[] = "\t  {$key}: {$value}";
            continue;
        }
        $diffs[] = "\t- {$key}: {$data1[$key]}";
        $diffs[] = "\t+ {$key}: {$value}";
    }
    $diffs[] = "}";

    return implode(PHP_EOL, $diffs) . PHP_EOL;
}
