<?php

namespace Differ;

use function Parser\parseFile;
use function Formatter\stylish;

function genDiff(string $filePath1, string $filePath2, string $formatter)
{
    $data1 = parseFile($filePath1);
    $data2 = parseFile($filePath2);

    $iter = function ($currentData1, $currentData2) use (&$iter) {
        $keys = array_merge($currentData1, $currentData2);
        ksort($keys);

        $diffs = [];
        foreach ($keys as $key => $value) {
            if (!key_exists($key, $currentData2)) {
                if (is_array($value)) {
                    $diffs[$key]['children'] = $value;
                    $diffs[$key]['sign'] = '-';
                    continue;
                }
                $diffs["{$key}: " . toString($value)] = '-';
                continue;
            }
            if (!key_exists($key, $currentData1)) {
                if (is_array($value)) {
                    $diffs[$key]['children'] = $value;
                    $diffs[$key]['sign'] = '+';
                    continue;
                }
                $diffs["{$key}: " . toString($value)] = '+';
                continue;
            }
            if (is_array($currentData1[$key]) && is_array($currentData2[$key])) {
                $diffs[$key]['children'] = $iter($currentData1[$key], $currentData2[$key]);
                continue;
            }
            if (is_array($currentData1[$key])) {
                $diffs[$key]['children'] = $currentData1[$key];
                $diffs[$key]['sign'] = '-';
                $diffs["{$key}: " . toString($currentData2[$key])] = '+';
                continue;
            }
            if (is_array($currentData2[$key])) {
                $diffs["{$key}: " . toString($currentData1[$key])] = '-';
                $diffs[$key]['children'] = $currentData2[$key];
                $diffs[$key]['sign'] = '+';
                continue;
            }
            if ($currentData1[$key] === $currentData2[$key]) {
                $diffs["{$key}: " . toString($value)] = ' ';
                continue;
            }
            $diffs["{$key}: " . toString($currentData1[$key])] = '-';
            $diffs["{$key}: " . toString($currentData2[$key])] = '+';
        }

        return $diffs;
    };
    $diffs = $iter($data1, $data2);
//dump($diffs);
    if ($formatter === 'stylish') {
        return stylish($diffs);
    }
}

function toString($value): string
{
    return trim(var_export($value, true), "'");
}
