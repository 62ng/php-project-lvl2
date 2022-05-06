<?php

namespace Formatter;

function stylish($diffs): string
{
    $iter = function ($currentDiffs, $depth, $signed = false) use (&$iter) {
        $indent = str_repeat("    ", $depth - 1);

        $lines = array_map(
            function ($key, $val) use ($indent, $iter, $depth, $signed) {
                if (is_array($val)) {
                    if (key_exists('sign', $val)) {
                        $indent .= "  {$val['sign']} ";
                        $signed = true;
                    } else {
                        $indent .= "    ";
                    }
                    $value = (key_exists('children', $val)) ? $val['children'] : $val;
                    return "{$indent}{$key}: {$iter($value, $depth + 1, $signed)}";
                }
                return "{$indent}" . ($signed ? "    {$key}: {$val}" : "  {$val} {$key}");
            },
            array_keys($currentDiffs),
            $currentDiffs
        );

        $result = ['{', ...$lines, "{$indent}}"];

        return implode(PHP_EOL, $result);
    };

    return $iter($diffs, 1);
}
