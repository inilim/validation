<?php

use Inilim\Tool\VD;
use Inilim\Tool\Path;
use Symfony\Component\Process\Process;

require_once __DIR__ . '/../vendor/autoload.php';

$bin = __DIR__ . '/../vendor/bin/phpunit';
$bin = Path::realPath($bin);
$bin = Path::normalize($bin);
$process = new Process(['php', $bin]);
$process->run();
$output = $process->getOutput();
echo $output;
