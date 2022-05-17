<?php

namespace Differ\Formatters\Json;

function formatToJson(array $diffs): string
{
    return json_encode($diffs);
}
