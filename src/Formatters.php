<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\stylish;
use function Differ\Formatters\Plain\plain;
use function Differ\Formatters\Json\json;

/**
 * Returns string implementation of data
 *
 * @param string|int|bool|null $value
 * @param bool $isQuoted
 * @return string
 */
function toString(string|int|bool|null $value, bool $isQuoted = false): string
{
    $valueString = trim(var_export($value, true), "' ");
    if (is_null($value)) {
        return strtolower($valueString);
    }
    if (is_bool($value) || is_int($value)) {
        return $valueString;
    }

    return $isQuoted ? "'{$valueString}'" : $valueString;
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
