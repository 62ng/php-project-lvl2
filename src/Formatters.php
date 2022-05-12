<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\stylish;
use function Differ\Formatters\Plain\plain;
use function Differ\Formatters\Json\json;

function toString($value): string
{
    $valueString = trim(var_export($value, true), "'");
    if (is_bool($value) || is_null($value)) {
        return strtolower($valueString);
    }

    return $valueString;
}

function format($diffs, $formatter)
{
    if ($formatter === 'stylish') {
        return stylish($diffs);
    } elseif ($formatter === 'plain') {
        return plain($diffs);
    } elseif ($formatter === 'json') {
        return json($diffs);
    } else {
        throw new \Exception('Unknown report format!');
    }
}
