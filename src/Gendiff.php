<?php

namespace Gendiff;

use Docopt;

function runGendiff()
{
    $doc = <<<DOC

Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)

Options:
  -h --help                     Show this screen
  -v --version                  Show version

DOC;

    $args = Docopt::handle($doc, array('version'=>'Gendiff 1.0'));
    foreach ($args as $k=>$v)
        echo $k.': '.json_encode($v).PHP_EOL;
}