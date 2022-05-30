<?php

namespace Differ\Formatters\Plain;

use function Differ\Formatters\toString;

function formatData(array $diffs): string
{
    $iter = function ($currentDiffs, $keyPath) use (&$iter) {
        $lines = array_map(
            function ($key, $val) use ($iter, $keyPath) {
                $keyPathCurrent = ($keyPath === '') ? (string) $key : "{$keyPath}.{$key}";

                if (key_exists('changed', $val)) {
                    return $iter($val['changed'], $keyPathCurrent);
                }

                return formatLine(
                    $keyPathCurrent,
                    $val['type'],
                    $val['deleted'] ?? null,
                    $val['added'] ?? null
                );
            },
            array_keys($currentDiffs),
            $currentDiffs
        );

        return implode(PHP_EOL, array_filter($lines));
    };

    return $iter($diffs, '');
}

function formatLine(string $path, string $type, mixed $valBefore, mixed $valAfter): string
{
    $stringedValBefore = (is_array($valBefore)) ? '[complex value]' : toString($valBefore, true);
    $stringedValAfter = (is_array($valAfter)) ? '[complex value]' : toString($valAfter, true);

    $line = "Property '{$path}' was";

    return match ($type) {
        'deleted' => $line . ' removed',
        'added' => $line . " added with value: {$stringedValAfter}",
        'changed' => $line . " updated. From {$stringedValBefore} to {$stringedValAfter}",
        default => ''
    };
}
