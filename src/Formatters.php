<?php

namespace Differ\Formatters;

use Differ\Formatters\Stylish as Stylish;
use Differ\Formatters\Plain as Plain;
use Differ\Formatters\Json as Json;

function toString(mixed $value, bool $isQuoted = false): string
{
    $valueString = trim(var_export($value, true), "' ");

    if (is_null($value)) {
        return mb_strtolower($valueString);
    }
    if (is_bool($value) || is_int($value)) {
        return $valueString;
    }

    return $isQuoted ? "'{$valueString}'" : $valueString;
}

function format(array $diffs, string $formatter): string
{
    return match ($formatter) {
        'stylish' => Stylish\formatData($diffs),
        'plain' => Plain\formatData($diffs),
        'json' => Json\formatData($diffs),
        default => throw new \Exception('Unknown report format!')
    };
}
