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
//dump($diffs);
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
            return [
                'key' => $key,
                'type' => 'deleted',
                'data' => ['before' => $currentData1[$key], 'after' => null]
            ];
        }

        if (!key_exists($key, $currentData1)) {
            return [
                'key' => $key,
                'type' => 'added',
                'data' => ['before' => null, 'after' => $currentData2[$key]]
            ];
        }

        if ($currentData1[$key] === $currentData2[$key]) {
            return [
                'key' => $key,
                'type' => 'unchanged',
                'data' => ['before' => $currentData1[$key], 'after' => null]
            ];
        }

        if (is_array($currentData1[$key]) && is_array($currentData2[$key])) {
            return [
                'key' => $key,
                'type' => 'nested',
                'data' => iter($currentData1[$key], $currentData2[$key])
            ];
        }

        return [
            'key' => $key,
            'type' => 'changed',
            'data' => ['before' => $currentData1[$key], 'after' => $currentData2[$key]]
        ];
    }, $allKeysSorted);
}
