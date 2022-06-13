<?php

namespace Differ\Formatters;

use Differ\Formatters\Stylish;
use Differ\Formatters\Plain;
use Differ\Formatters\Json;

function toString(mixed $value, bool $isNeedQuotes = false): string
{
    if (is_null($value)) {
        return 'null';
    }

    $exportedValue = var_export($value, true);

    if (is_bool($value) || is_int($value)) {
        return $exportedValue;
    }

    $trimedValue = trim($exportedValue, "' ");
    return $isNeedQuotes ? "'{$trimedValue}'" : $trimedValue;
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
