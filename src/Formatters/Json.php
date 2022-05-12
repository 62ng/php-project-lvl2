<?php

namespace Differ\Formatters\Json;

function json(array $diffs): string
{
    return json_encode($diffs);
}
