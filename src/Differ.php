<?php

namespace Differ\Differ;

use function Differ\Formatters\format;
use function Differ\Parser\parseData;
use function Functional\sort;

function genDiff(string $filePath1, string $filePath2, string $formatter = 'stylish')
{
    $data1 = getContent($filePath1);
    $data2 = getContent($filePath2);

    $diffs = iter($data1, $data2);

    return format($diffs, $formatter);
}

function getContent(string $filePath): array
{
    if (!file_exists($filePath)) {
        throw new \Exception('Incorrect file path!');
    }

    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    $fileData = (string) file_get_contents($filePath);

    return parseData($fileData, $extension);
}

function iter(array $currentData1, array $currentData2): array
{
    $mergedData = array_merge($currentData1, $currentData2);
    $allKeys = array_keys($mergedData);
    $allKeysSorted = sort($allKeys, fn ($left, $right) => strcmp($left, $right));

    return array_map(function ($keyIndex) use ($allKeysSorted, $currentData1, $currentData2) {
        $key = $allKeysSorted[$keyIndex];

        if (!key_exists($key, $currentData2)) {
            return ['type' => 'deletedElement', 'deletedElement' => $currentData1[$key]];
        }

        if (!key_exists($key, $currentData1)) {
            return ['type' => 'addedElement', 'addedElement' => $currentData2[$key]];
        }

        if ($currentData1[$key] === $currentData2[$key]) {
            return ['type' => 'unchangedElement', 'unchangedElement' => $currentData1[$key]];
        }

        if (is_array($currentData1[$key]) && is_array($currentData2[$key])) {
            return ['type' => 'changedElement', 'changedElement' => iter($currentData1[$key], $currentData2[$key])];
        }

        return [
            'type' => 'changedElement',
            'deletedElement' => $currentData1[$key],
            'addedElement' => $currentData2[$key]
        ];
    }, array_flip($allKeysSorted));
}
