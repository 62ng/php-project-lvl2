<?php

namespace Differ\Formatters\Plain;

use function Differ\Formatters\toString;

function plain($diffs): string
{
    $iter = function ($currentDiffs, $keyPath) use (&$iter) {
        $lines = array_map(
            function ($key, $val) use ($iter, $keyPath) {
                $keyPath = ($keyPath === '') ? $key : "{$keyPath}.{$key}";
                if ($val['type'] === 'deleted') {
                    return "Property '{$keyPath}' was removed";
                }
                if ($val['type'] === 'added') {
                    $value = (is_array($val['added'])) ? '[complex value]' : quotingIfString($val['added']);
                    return "Property '{$keyPath}' was added with value: {$value}";
                }
                if ($val['type'] === 'changed') {
                    if (key_exists('changed', $val)) {
                        return $iter($val['changed'], $keyPath);
                    } else {
                        $valueBefore = (is_array($val['deleted']))
                            ? '[complex value]'
                            : quotingIfString($val['deleted']);
                        $valueAfter = (is_array($val['added'])) ? '[complex value]' : quotingIfString($val['added']);
                        return "Property '{$keyPath}' was updated. From {$valueBefore} to {$valueAfter}";
                    }
                }
            },
            array_keys($currentDiffs),
            $currentDiffs
        );
        $lines = array_filter($lines, fn ($val) => $val);

        return implode(PHP_EOL, $lines);
    };

//    return dump($iter($diffs, ''));
    return $iter($diffs, '');
}

function quotingIfString($value): string
{
    if (is_bool($value) || is_null($value) || is_int($value)) {
        return toString($value);
    }

    return "'" . toString($value) . "'";
}
