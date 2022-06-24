<?php

namespace Differ\Differ;

use function Differ\Formatters\format;
use function Differ\Parser\parseData;
use function Functional\sort;

function genDiff(string $filePath1, string $filePath2, string $formatter = 'stylish')
{
    $data1 = getFileData($filePath1);
    $data2 = getFileData($filePath2);

    $content1 = parseData($data1['file'], $data1['type']);
    $content2 = parseData($data2['file'], $data2['type']);

    $diffs = makeTree($content1, $content2);

    return format($diffs, $formatter);
}

function getFileData(string $filePath): array
{
    if (!file_exists($filePath)) {
        throw new \Exception('Incorrect file path!');
    }

    $fileType = pathinfo($filePath, PATHINFO_EXTENSION);
    $fileData = (string) file_get_contents($filePath);

    return ['file' => $fileData, 'type' => $fileType];
}

function makeTree(array $currentData1, array $currentData2): array
{
    $mergedData = array_merge($currentData1, $currentData2);
    $allKeys = array_keys($mergedData);
    $allKeysSorted = sort($allKeys, fn ($left, $right) => strcmp($left, $right));

    return array_map(function ($key) use ($currentData1, $currentData2) {

        if (!key_exists($key, $currentData2)) {
            return [
                'key' => $key,
                'type' => 'deleted',
                'children' => ['first' => $currentData1[$key], 'second' => null]
            ];
        }

        if (!key_exists($key, $currentData1)) {
            return [
                'key' => $key,
                'type' => 'added',
                'children' => ['first' => null, 'second' => $currentData2[$key]]
            ];
        }

        if ($currentData1[$key] === $currentData2[$key]) {
            return [
                'key' => $key,
                'type' => 'unchanged',
                'children' => ['first' => $currentData1[$key], 'second' => null]
            ];
        }

        if (is_array($currentData1[$key]) && is_array($currentData2[$key])) {
            return [
                'key' => $key,
                'type' => 'nested',
                'children' => makeTree($currentData1[$key], $currentData2[$key])
            ];
        }

        return [
            'key' => $key,
            'type' => 'changed',
            'children' => ['first' => $currentData1[$key], 'second' => $currentData2[$key]]
        ];
    }, $allKeysSorted);
}
