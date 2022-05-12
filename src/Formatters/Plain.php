<?php

namespace Differ\Formatters\Plain;

use function Differ\Formatters\toString;

function plain(array $diffs): string
{
    $iter = function ($currentDiffs, $keyPath) use (&$iter) {
        $lines = array_map(
            function ($key, $val) use ($iter, $keyPath) {
                $keyPath = ($keyPath === '') ? $key : "{$keyPath}.{$key}";
                if ($val['type'] === 'deleted') {
                    return "Property '{$keyPath}' was removed";
                }
                if ($val['type'] === 'added') {
                    $value = (is_array($val['added'])) ? '[complex value]' : toString($val['added'], true);
                    return "Property '{$keyPath}' was added with value: {$value}";
                }
                if ($val['type'] === 'changed') {
                    if (key_exists('changed', $val)) {
                        return $iter($val['changed'], $keyPath);
                    } else {
                        $valBefore = (is_array($val['deleted'])) ? '[complex value]' : toString($val['deleted'], true);
                        $valAfter = (is_array($val['added'])) ? '[complex value]' : toString($val['added'], true);
                        return "Property '{$keyPath}' was updated. From {$valBefore} to {$valAfter}";
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
