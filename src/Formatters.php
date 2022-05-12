<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\stylish;
use function Differ\Formatters\Plain\plain;
use function Differ\Formatters\Json\json;

//function toString($value): string
//{
//    return trim(var_export($value, true), "'");
//}
//
function toString($value): string
{
    $valueString = trim(var_export($value, true), "'");
    if (is_bool($value) || is_null($value)) {
        return strtolower($valueString);
    }

    return $valueString;
}

function valueFormat(string $value, bool $quotation = true): string
{
    $value = trim($value, "'");
    if ($value === 'NULL') {
        return 'null';
    } elseif ($value === 'true' || $value === 'false') {
        return $value;
    }

    return $quotation ? "'{$value}'" : $value;
}

function format(array $diffs, string $formatter): string
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
