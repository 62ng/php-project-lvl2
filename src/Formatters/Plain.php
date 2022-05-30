<?php

namespace Differ\Formatters\Plain;

use function Differ\Formatters\toString;

function formatData(array $diffs): string
{
    $iter = function ($currentDiffs, $keyPath) use (&$iter) {
        $lines = array_map(
            function ($key, $value) use ($iter, $keyPath) {
                $keyPathCurrent = ($keyPath === '') ? (string) $key : "{$keyPath}.{$key}";

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
            array_keys($currentDiffs),
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
        'deletedElement' => $line . ' removed',
        'addedElement' => $line . " added with value: {$stringedValueAfter}",
        'changedElement' => $line . " updated. From {$stringedValueBefore} to {$stringedValueAfter}",
        default => ''
    };
}
