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
        if (!is_array($currentDiffs)) {
            return toString($currentDiffs);
        }

        $lines = array_map(
            function ($key, $val) use ($iter, $depth) {
                if (!is_array($val)) {
                    return formatLine($depth, 'unchanged', $key, $val);
                }

                if (!key_exists('type', $val)) {
                    return formatLine($depth, 'unchanged', $key, $iter($val, $depth + 1));
                }

                if (!key_exists($val['type'], $val)) {
                    return formatLine($depth, 'deleted', $key, $iter($val['deleted'], $depth + 1)) . PHP_EOL
                        . formatLine($depth, 'added', $key, $iter($val['added'], $depth + 1));
                }

                return formatLine($depth, $val['type'], $key, $iter($val[$val['type']], $depth + 1));
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

function formatLine(int $depth, string $type, mixed $key, mixed $val): string
{
    return makeIndent($depth, TYPE_SYMBOLS[$type]) . "{$key}: {$val}";
}
