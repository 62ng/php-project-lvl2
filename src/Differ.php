<?php

namespace Differ\Differ;

use function Differ\Parser\parseFile;
use function Differ\Formatters\format;
use function Functional\sort;

function genDiff(string $filePath1, string $filePath2, string $formatter = 'stylish')
{
    $data1 = parseFile($filePath1);
    $data2 = parseFile($filePath2);

    $iter = function ($currentData1, $currentData2) use (&$iter) {
        $mergedData = array_merge($currentData1, $currentData2);
        $allKeys = array_keys($mergedData);
        $allKeysSorted = sort($allKeys, fn ($left, $right) => strcmp($left, $right));

        $diffs = [];
        foreach ($allKeysSorted as $key) {
            if (!key_exists($key, $currentData2)) {
                $diffs[$key]['type'] = 'deleted';
                $diffs[$key]['deleted'] = $currentData1[$key];
                continue;
            }
            if (!key_exists($key, $currentData1)) {
                $diffs[$key]['type'] = 'added';
                $diffs[$key]['added'] = $currentData2[$key];
                continue;
            }
            if ($currentData1[$key] === $currentData2[$key]) {
                $diffs[$key]['type'] = 'unchanged';
                $diffs[$key]['unchanged'] = $currentData1[$key];
                continue;
            }
            $diffs[$key]['type'] = 'changed';
            if (is_array($currentData1[$key]) && is_array($currentData2[$key])) {
                $diffs[$key]['changed'] = $iter($currentData1[$key], $currentData2[$key]);
                continue;
            }
            $diffs[$key]['deleted'] = $currentData1[$key];
            $diffs[$key]['added'] = $currentData2[$key];
        }

        return $diffs;
    };
    $diffs = $iter($data1, $data2);

    return format($diffs, $formatter);
}
