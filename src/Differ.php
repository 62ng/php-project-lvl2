<?php

namespace Differ\Differ;

use function Differ\Parser\getContent;
use function Differ\Formatters\format;
use function Functional\sort;

function genDiff(string $filePath1, string $filePath2, string $formatter = 'stylish')
{
    $data1 = getContent($filePath1);
    $data2 = getContent($filePath2);

    $diffs = iter($data1, $data2);

    return format($diffs, $formatter);
}

function iter(array $currentData1, array $currentData2): array
{
    $mergedData = array_merge($currentData1, $currentData2);
    $allKeys = array_keys($mergedData);
    $allKeysSorted = sort($allKeys, fn ($left, $right) => strcmp($left, $right));

    return array_map(function ($keyIndex) use ($allKeysSorted, $currentData1, $currentData2) {
        $key = $allKeysSorted[$keyIndex];

        if (!key_exists($key, $currentData2)) {
            return ['type' => 'deleted', 'deleted' => $currentData1[$key]];
        }

        if (!key_exists($key, $currentData1)) {
            return ['type' => 'added', 'added' => $currentData2[$key]];
        }

        if ($currentData1[$key] === $currentData2[$key]) {
            return ['type' => 'unchanged', 'unchanged' => $currentData1[$key]];
        }

        if (is_array($currentData1[$key]) && is_array($currentData2[$key])) {
            return ['type' => 'changed', 'changed' => iter($currentData1[$key], $currentData2[$key])];
        }

        return ['type' => 'changed', 'deleted' => $currentData1[$key], 'added' => $currentData2[$key]];
    }, array_flip($allKeysSorted));
}
