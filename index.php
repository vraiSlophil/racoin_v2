<?php

declare(strict_types=1);

$dependances = require __DIR__ . '/bootstrap/app.php';

$app    = $dependances['app'];
$twig   = $dependances['twig'];
$menu   = $dependances['menu'];
$chemin = $dependances['chemin'];
$cat    = $dependances['cat'];
$dpt    = $dependances['dpt'];

require __DIR__ . '/src/Routes/web.php';
require __DIR__ . '/src/Routes/api.php';

$app->run();
