#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\OpenApi\OpenApiDocumentFactory;

$factory = new OpenApiDocumentFactory(dirname(__DIR__) . '/src/OpenApi');
$document = $factory->generateJson('/');
$outputPath = $argv[1] ?? null;

if ($outputPath === null) {
    fwrite(STDOUT, $document . PHP_EOL);

    exit(0);
}

$outputDirectory = dirname($outputPath);

if ($outputDirectory !== '.' && !is_dir($outputDirectory) && !mkdir($outputDirectory, 0777, true) && !is_dir($outputDirectory)) {
    throw new RuntimeException(sprintf('Unable to create output directory "%s".', $outputDirectory));
}

if (file_put_contents($outputPath, $document . PHP_EOL) === false) {
    throw new RuntimeException(sprintf('Unable to write OpenAPI document to "%s".', $outputPath));
}

fwrite(STDOUT, sprintf("OpenAPI document written to %s\n", $outputPath));
