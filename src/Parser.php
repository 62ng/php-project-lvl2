<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function getContent(string $filePath): string
{
    if (!file_exists($filePath)) {
        throw new \Exception('Incorrect file path!');
    }

    return file_get_contents($filePath);
}

function parseFile(string $filePath)
{
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    $fileData = getContent($filePath);

    return match ($extension) {
        'yaml', 'yml' => Yaml::parse($fileData),
        'json' => json_decode($fileData, true),
        default => throw new \Exception('Unknown file extension!')
    };
}
