<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function parseData(string $fileData, string $fileType): array
{
    return match ($fileType) {
        'yaml', 'yml' => Yaml::parse($fileData),
        'json' => json_decode($fileData, true),
        default => throw new \Exception('Unknown file type!')
    };
}
