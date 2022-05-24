<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function getContent(string $filePath): array
{
    if (!file_exists($filePath)) {
        throw new \Exception('Incorrect file path!');
    }

    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    $fileData = file_get_contents($filePath);

    return parseFile($fileData, $extension);
}

function parseFile($fileData, string $extension): array
{
    return match ($extension) {
        'yaml', 'yml' => Yaml::parse($fileData),
        'json' => json_decode($fileData, true),
        default => throw new \Exception('Unknown file extension!')
    };
}
