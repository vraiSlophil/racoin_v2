<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$configFile = $root . '/config/config.ini';
$configDistFile = $root . '/config/config.ini.dist';

if (!file_exists($configFile) && file_exists($configDistFile)) {
    copy($configDistFile, $configFile);
}

require $root . '/vendor/autoload.php';
