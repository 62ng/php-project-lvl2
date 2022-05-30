<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function parseData(string $fileData, string $extension): array
{
    return match ($extension) {
        'yaml', 'yml' => Yaml::parse($fileData),
        'json' => json_decode($fileData, true),
        default => throw new \Exception('Unknown file extension!')
    };
}
