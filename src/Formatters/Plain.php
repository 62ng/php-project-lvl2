<?php

namespace Differ\Formatters\Plain;

use function Differ\Formatters\toString;

function formatData(array $diffs): string
{
    $iter = function ($currentDiffs, $keyPath) use (&$iter) {
        $lines = array_map(
            function ($value) use ($iter, $keyPath) {
                $keyPathCurrent = ($keyPath === '') ? (string) $value['key'] : "{$keyPath}.{$value['key']}";

                if (key_exists('changedElement', $value)) {
                    return $iter($value['changedElement'], $keyPathCurrent);
                }

                return formatLine(
                    $keyPathCurrent,
                    $value['type'],
                    $value['deletedElement'] ?? null,
                    $value['addedElement'] ?? null
                );
            },
            $currentDiffs
        );

        return implode(PHP_EOL, array_filter($lines));
    };

    return $iter($diffs, '');
}

function formatLine(string $path, string $type, mixed $valueBefore, mixed $valueAfter): string
{
    $stringedValueBefore = (is_array($valueBefore)) ? '[complex value]' : toString($valueBefore, true);
    $stringedValueAfter = (is_array($valueAfter)) ? '[complex value]' : toString($valueAfter, true);

    $line = "Property '{$path}' was";

    return match ($type) {
        'deleted' => $line . ' removed',
        'added' => $line . " added with value: {$stringedValueAfter}",
        'changed' => $line . " updated. From {$stringedValueBefore} to {$stringedValueAfter}",
        default => ''
    };
}
