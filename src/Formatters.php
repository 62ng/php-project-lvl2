<?php

namespace Formatters;

use function Formatters\Stylish\stylish;
use function Formatters\Plain\plain;

function toString($value): string
{
    return trim(var_export($value, true), "'");
}

function format($diffs, $formatter)
{
    if ($formatter === 'stylish') {
        return stylish($diffs);
    } elseif ($formatter === 'plain') {
        return plain($diffs);
    }
}
