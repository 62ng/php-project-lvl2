<?php

namespace Differ\Formatters\Stylish;

use function Differ\Formatters\toString;

function formatData(array $diffs): string
{
    $iter = function ($currentDiffs, $depth) use (&$iter) {

        $lines = array_map(
            function ($node) use ($iter, $depth) {
                return match ($node['type']) {
                    'nested' => stringify($depth, ' ', $node['key'], $iter($node['children'], $depth + 1)),

                    'changed' => stringify($depth, '-', $node['key'], $node['children']['first'])
                        . PHP_EOL
                        . stringify($depth, '+', $node['key'], $node['children']['second']),

                    'deleted'  => stringify($depth, '-', $node['key'], $node['children']['first']),

                    'unchanged' => stringify($depth, ' ', $node['key'], $node['children']['first']),

                    'added' => stringify($depth, '+', $node['key'], $node['children']['second']),

                    default => throw new \Exception('Unknown node type!')
                };
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

function stringify(int $depth, string $sign, mixed $key, mixed $nodeData): string
{
    $prefix = ($key === '') ? '' : makeIndent($depth, $sign) . "{$key}: ";

    if (!is_array($nodeData)) {
        return $prefix . trim(toString($nodeData), "'");
    }

    $lines = array_map(
        function ($key, $value) use ($depth, $sign) {

            $prefix = makeIndent($depth + 1, ' ') . "{$key}: ";

            if (is_array($value)) {
                return $prefix . stringify($depth + 1, $sign, '', $value);
            }

            return $prefix . $value;
        },
        array_keys($nodeData),
        $nodeData
    );

    return $prefix . implode(PHP_EOL, ['{', ...$lines, makeIndent($depth + 1, '}') . '}']);
}
