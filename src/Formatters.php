<?php

namespace Differ\Formatters;

use Differ\Formatters\Stylish;
use Differ\Formatters\Plain;
use Differ\Formatters\Json;

function toString(mixed $value): string
{
    if (is_null($value)) {
        return 'null';
    }

    return var_export($value, true);
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
