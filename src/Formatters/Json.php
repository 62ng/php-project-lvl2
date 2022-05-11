<?php

namespace Differ\Formatters\Json;

function json($diffs): string
{
    return json_encode($diffs);
}
