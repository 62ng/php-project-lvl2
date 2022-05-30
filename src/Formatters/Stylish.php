<?php

namespace Differ\Formatters\Stylish;

use function Differ\Formatters\toString;

const TYPE_SYMBOLS = [
    'deletedElement' => '-',
    'addedElement' => '+',
    'changedElement' => ' ',
    'unchangedElement' => ' '
];

function formatData(array $diffs): string
{
    $iter = function ($currentDiffs, $depth) use (&$iter) {
        if (!is_array($currentDiffs)) {
            return toString($currentDiffs);
        }

        $lines = array_map(
            function ($key, $value) use ($iter, $depth) {
                if (!is_array($value)) {
                    return formatLine($depth, 'unchangedElement', $key, $value);
                }

                if (!key_exists('type', $value)) {
                    return formatLine($depth, 'unchangedElement', $key, $iter($value, $depth + 1));
                }

                if (!key_exists($value['type'], $value)) {
                    return formatLine($depth, 'deletedElement', $key, $iter($value['deletedElement'], $depth + 1))
                        . PHP_EOL
                        . formatLine($depth, 'addedElement', $key, $iter($value['addedElement'], $depth + 1));
                }

                return formatLine($depth, $value['type'], $key, $iter($value[$value['type']], $depth + 1));
            },
            array_keys($currentDiffs),
            $currentDiffs
        );

        return implode(PHP_EOL, ['{', ...$lines, makeIndent($depth, '}') . '}']);
    };

    return $iter($diffs, 0);
}

function makeIndent(int $depth, string $sign = ' '): string
{
    return str_repeat("    ", $depth) . ($sign === '}' ? '' : "  {$sign} ");
}

function formatLine(int $depth, string $type, mixed $key, mixed $value): string
{
    return makeIndent($depth, TYPE_SYMBOLS[$type]) . "{$key}: {$value}";
}
