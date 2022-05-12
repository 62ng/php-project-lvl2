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
    if (in_array($extension, ['yaml', 'yml'], true)) {
        return Yaml::parse((string) $fileData);
    } elseif ($extension === 'json') {
        return json_decode((string) $fileData, true);
    } else {
        throw new \Exception('Unknown file extension!');
    }
}
