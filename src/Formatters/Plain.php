<?php

namespace Differ\Formatters\Plain;

use function Differ\Formatters\toString;

function formatData(array $diffs): string
{
    $iter = function ($currentDiffs, $keyPath) use (&$iter) {
        $lines = array_map(
            function ($node) use ($iter, $keyPath) {
                $keyPathCurrent = ($keyPath === '') ? (string) $node['key'] : "{$keyPath}.{$node['key']}";
                $line = "Property '{$keyPathCurrent}' was";

                return match ($node['type']) {
                    'nested' => $iter($node['children'], $keyPathCurrent),
                    'deleted' => $line . ' removed',
                    'added' => $line . " added with value: " . stringify($node['children']['second']),
                    'changed' => $line . " updated. From "
                        . stringify($node['children']['first']) . " to "
                        . stringify($node['children']['second']),
                    'unchanged' => '',
                    default => throw new \Exception('Unknown node type!')
                };
            },
            $currentDiffs
        );

        return implode(PHP_EOL, array_filter($lines));
    };

    return $iter($diffs, '');
}

function stringify(mixed $nodeData): string
{
    return (is_array($nodeData)) ? '[complex value]' : toString($nodeData);
}
