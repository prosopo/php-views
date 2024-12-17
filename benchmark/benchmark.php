<?php

// external packages.
require __DIR__ . '/vendor/autoload.php';
// the current package.
require __DIR__ . '/../vendor/autoload.php';

$bench = new Benchmark\Benchmark();
$bench->run();
