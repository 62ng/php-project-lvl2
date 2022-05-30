<?php

namespace Differ\Formatters\Json;

function formatData(array $diffs): string
{
    return json_encode($diffs);
}
