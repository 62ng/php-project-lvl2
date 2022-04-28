<?php

namespace YamlDiffer;

use function Parsers\parseFile;

function genDiff(string $filePath1, string $filePath2): string
{
    $data1 = parseFile($filePath1);
    $data2 = parseFile($filePath2);

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
