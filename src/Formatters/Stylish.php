<?php

namespace Differ\Formatters\Stylish;

use function Differ\Formatters\toString;

const TYPE_SYMBOLS = [
    'deleted' => '-',
    'added' => '+',
    'changed' => ' ',
    'unchanged' => ' '
];

function indent(int $depth, string $sign = ' '): string
{
    return str_repeat("    ", $depth) . ($sign === '}' ? '' : "  {$sign} ");
}

function formatToStylish(array $diffs): string
{
    $iter = function ($currentDiffs, $depth) use (&$iter) {
        if (!is_array($currentDiffs)) {
            return toString($currentDiffs);
        }

        $lines = array_map(
            function ($key, $val) use ($iter, $depth) {
                if (!is_array($val)) {
                    return indent($depth) . "{$key}: " . toString($val);
                }
                if (!key_exists('type', $val)) {
                    return indent($depth) . "{$key}: {$iter($val, $depth + 1)}";
                }
                if (!key_exists($val['type'], $val)) {
                    return indent($depth, TYPE_SYMBOLS['deleted']) . "{$key}: {$iter($val['deleted'], $depth + 1)}"
                        . PHP_EOL
                        . indent($depth, TYPE_SYMBOLS['added']) . "{$key}: {$iter($val['added'], $depth + 1)}";
                }
                return indent($depth, TYPE_SYMBOLS[$val['type']]) . "{$key}: {$iter($val[$val['type']], $depth + 1)}";
            },
            array_keys($currentDiffs),
            $currentDiffs
        );

        return implode(PHP_EOL, ['{', ...$lines, indent($depth, '}') . '}']);
    };

    return $iter($diffs, 0);
}
