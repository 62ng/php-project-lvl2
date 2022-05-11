<?php

namespace Differ\Cli;

use Docopt;

function runDocopt()
{
    $doc = <<<DOC

Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)
  gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  -v --version                  Show version
  --format <fmt>                Report format [default: stylish]
DOC;

    return Docopt::handle($doc, array('version' => 'Gendiff 1.0'));
}
