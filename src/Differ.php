<?php

namespace Differ\Differ;

use function Differ\Formatters\format;
use function Differ\Parser\parseData;
use function Functional\sort;

function genDiff(string $filePath1, string $filePath2, string $formatter = 'stylish')
{
    $data1 = getFileData($filePath1);
    $data2 = getFileData($filePath2);

    $content1 = parseData($data1['file'], $data1['extension']);
    $content2 = parseData($data2['file'], $data2['extension']);

    $diffs = iter($content1, $content2);

    return format($diffs, $formatter);
}

function getFileData(string $filePath): array
{
    if (!file_exists($filePath)) {
        throw new \Exception('Incorrect file path!');
    }

    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    $fileData = (string) file_get_contents($filePath);

    return ['file' => $fileData, 'extension' => $extension];
}

function iter(array $currentData1, array $currentData2): array
{
    $mergedData = array_merge($currentData1, $currentData2);
    $allKeys = array_keys($mergedData);
    $allKeysSorted = sort($allKeys, fn ($left, $right) => strcmp($left, $right));

    return array_map(function ($key) use ($currentData1, $currentData2) {

        if (!key_exists($key, $currentData2)) {
            return ['key' => $key, 'type' => 'deleted', 'deletedElement' => $currentData1[$key]];
        }

        if (!key_exists($key, $currentData1)) {
            return ['key' => $key, 'type' => 'added', 'addedElement' => $currentData2[$key]];
        }

        if ($currentData1[$key] === $currentData2[$key]) {
            return ['key' => $key, 'type' => 'unchanged', 'unchangedElement' => $currentData1[$key]];
        }

        if (is_array($currentData1[$key]) && is_array($currentData2[$key])) {
            return [
                'key' => $key, 'type' => 'changed', 'changedElement' => iter($currentData1[$key], $currentData2[$key])
            ];
        }

        return [
            'key' => $key,
            'type' => 'changed',
            'deletedElement' => $currentData1[$key],
            'addedElement' => $currentData2[$key]
        ];
    }, $allKeysSorted);
}
