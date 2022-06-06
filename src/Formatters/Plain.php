<?php

namespace Differ\Formatters\Plain;

use function Differ\Formatters\toString;

function formatData(array $diffs): string
{
    $iter = function ($currentDiffs, $keyPath) use (&$iter) {
        $lines = array_map(
            function ($node) use ($iter, $keyPath) {
                $keyPathCurrent = ($keyPath === '') ? (string) $node['key'] : "{$keyPath}.{$node['key']}";

                if ($node['type'] === 'mixed') {
                    return $iter($node['data'], $keyPathCurrent);
                }

                return formatLine(
                    $keyPathCurrent,
                    $node['type'],
                    $node['data']['before'],
                    $node['data']['after']
                );
            },
            $currentDiffs
        );

        return implode(PHP_EOL, array_filter($lines));
    };

    return $iter($diffs, '');
}

function formatLine(string $path, string $type, mixed $dataBefore, mixed $dataAfter): string
{
    $line = "Property '{$path}' was";

    return match ($type) {
        'deleted' => $line . ' removed',
        'added' => $line . " added with value: " . stringify($dataAfter),
        'changed' => $line . " updated. From " . stringify($dataBefore) . " to " . stringify($dataAfter),
        default => ''
    };
}

function stringify(mixed $nodeData): string
{
    return (is_array($nodeData)) ? '[complex value]' : toString($nodeData, true);
}
