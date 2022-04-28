<?php

namespace Parsers;

use Symfony\Component\Yaml\Yaml;

function parseFile(string $filePath): array
{
    if (!file_exists($filePath)) {
        throw new \Exception('Incorrect file path!');
    }

    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    $fileData = file_get_contents($filePath);
    if (in_array($extension, ['yaml', 'yml'])) {
        return Yaml::parse($fileData);
    } elseif ($extension === 'json') {
        return json_decode($fileData, true);
    } else {
        throw new \Exception('Unknown file extension!');
    }
}
