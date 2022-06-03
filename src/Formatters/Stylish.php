<?php

namespace Differ\Formatters\Stylish;

use function Differ\Formatters\toString;

const TYPE_SYMBOLS = [
    'deleted' => '-',
    'added' => '+',
    'changed' => ' ',
    'unchanged' => ' '
];

function formatData(array $diffs): string
{
    $iter = function ($currentDiffs, $depth) use (&$iter) {

        $lines = array_map(
            function ($value) use ($iter, $depth) {

                if (key_exists('changedElement', $value)) {
                    return formatLine($depth, 'changed', $value['key'], $iter($value['changedElement'], $depth + 1));
                }

                if ($value['type'] === 'changed') {
                    return formatLine($depth, 'deleted', $value['key'], $value['deletedElement'])
                        . PHP_EOL
                        . formatLine($depth, 'added', $value['key'], $value['addedElement']);
                }

                return formatLine($depth, $value['type'], $value['key'], $value[$value['type'] . 'Element']);
            },
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
    return makeIndent($depth, TYPE_SYMBOLS[$type]) . "{$key}: " . formatValue($depth + 1, $value);
}

function formatValue(int $depth, mixed $value): string
{
    if (!is_array($value)) {
        return toString($value);
    }

    $lines = array_map(
        function ($key, $value) use ($depth) {
            if (is_array($value)) {
                return makeIndent($depth, ' ') . "{$key}: " . formatValue($depth + 1, $value);
            }

            return makeIndent($depth, ' ') . "{$key}: {$value}";
        },
        array_keys($value),
        $value
    );

    return implode(PHP_EOL, ['{', ...$lines, makeIndent($depth, '}') . '}']);
}
