<?php

use Inilim\Tool\VD;
use Inilim\Tool\File;
use Inilim\Tool\Path;
use Symfony\Component\Process\Process;

require_once __DIR__ . '/../vendor/autoload.php';

$root = __DIR__ . '/../';
$root = Path::realPath($root);
$bin = $root . '/vendor/bin/phpunit';
$bin = Path::normalize($bin);
$process = new Process(['php', $bin]);
$process->run();
$output = $process->getOutput();

$fileOutput = Path::normalize($root . '/files/phpunit-last-output.txt');
File::put($fileOutput, $output);

echo $output;
echo PHP_EOL . \sprintf('Output file "%s"', $fileOutput);
