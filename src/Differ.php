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

//    Мне нужно построить AST-дерево на основе двух массивов, слитых в одно, с сохранением ключей
//    Т.к. нет чистой сортировки по ключам, приходится сортировать ключи отдельно и далее маппить полученный
//    отсортированный массив. Но у этого массива собственные ключи числовые и маппить его напрямую дает массив
//    с числовыми же ключами. Поэтому я переворачиваю отсортированные ключи. Альтернативой
//    вижу написание собственной рекурсивной функции сортировки по ключам, либо использование array_reduce.
//    Но оба варианта выглядят более дорогими, чем добавление одной такой строчки $key = $allKeysSorted[$keyIndex];
//    Может, я какого-то очевидного хода не вижу, но уже несколько раз рассматривал этот кусок
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
