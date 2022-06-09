<?php

namespace Differ\Formatters\Stylish;

use function Differ\Formatters\toString;

const TYPE_SYMBOLS = [
    'deleted' => '-',
    'added' => '+',
    'changed' => ' ',
    'unchanged' => ' ',
    'nested' => ' '
];

function formatData(array $diffs): string
{
    $iter = function ($currentDiffs, $depth) use (&$iter) {

        $lines = array_map(
            function ($node) use ($iter, $depth) {
                return match ($node['type']) {
                    'nested' => formatLine($depth, 'nested', $node['key'], $iter($node['data'], $depth + 1)),
                    'changed' => formatLine($depth, 'deleted', $node['key'], $node['data']['before'])
                        . PHP_EOL
                        . formatLine($depth, 'added', $node['key'], $node['data']['after']),
                    'deleted', 'unchanged' => formatLine($depth, $node['type'], $node['key'], $node['data']['before']),
                    'added' => formatLine($depth, $node['type'], $node['key'], $node['data']['after']),
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

function formatLine(int $depth, string $type, mixed $key, mixed $nodeData): string
{
    return makeIndent($depth, TYPE_SYMBOLS[$type]) . "{$key}: " . stringify($depth + 1, $type, $nodeData);
}

function stringify(int $depth, string $type, mixed $nodeData): string
{
    if (!is_array($nodeData)) {
        return toString($nodeData);
    }

    $lines = array_map(
        function ($key, $value) use ($depth, $type) {
            if (is_array($value)) {
                return makeIndent($depth, ' ') . "{$key}: " . stringify($depth + 1, $type, $value);
            }

            return makeIndent($depth, ' ') . "{$key}: {$value}";
        },
        array_keys($nodeData),
        $nodeData
    );

    return implode(PHP_EOL, ['{', ...$lines, makeIndent($depth, '}') . '}']);
}
