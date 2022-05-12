<?php

namespace Differ\Formatters\Stylish;

use function Differ\Formatters\toString;

function stylish(array $diffs): string
{
    $types = [
        'deleted' => '-',
        'added' => '+',
        'changed' => ' ',
        'unchanged' => ' ',
        'null' => ' '
    ];
    $iter = function ($currentDiffs, $depth) use (&$iter, $types) {
        if (!is_array($currentDiffs)) {
            return toString($currentDiffs);
        }

        $indent = str_repeat("    ", $depth - 1);

        $lines = array_map(
            function ($key, $val) use ($iter, $indent, $depth, $types) {
                if (!is_array($val)) {
                    $indent = str_repeat("    ", $depth);
                    return "{$indent}{$key}: " . toString($val);
                }
                if (!key_exists('type', $val)) {
                    $currentDiff = $val;
                    $val['type'] = 'null';
                } elseif (!key_exists($val['type'], $val)) {
                    return "{$indent}  {$types['deleted']} {$key}: {$iter($val['deleted'], $depth + 1)}" . PHP_EOL
                        . "{$indent}  {$types['added']} {$key}: {$iter($val['added'], $depth + 1)}";
                } else {
                    $currentDiff = $val[$val['type']];
                }
                return "{$indent}  {$types[$val['type']]} {$key}: {$iter($currentDiff, $depth + 1)}";
            },
            array_keys($currentDiffs),
            $currentDiffs
        );

        $result = ['{', ...$lines, "{$indent}}"];

        return implode(PHP_EOL, $result);
    };

//    return dump($iter($diffs, 1));
    return $iter($diffs, 1);
}
