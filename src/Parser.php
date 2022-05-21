<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function parseFile(string $filePath)
{
    if (!file_exists($filePath)) {
        throw new \Exception('Incorrect file path!');
    }

    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    $fileData = file_get_contents($filePath);

    return match ($extension) {
        'yaml' => Yaml::parse((string) $fileData),
        'json' => json_decode((string) $fileData, true),
        default => throw new \Exception('Unknown file extension!')
    };
}
