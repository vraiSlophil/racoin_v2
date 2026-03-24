<?php

$dependances = require __DIR__ . '/bootstrap/app.php';

$app    = $dependances['app'];
$twig   = $dependances['twig'];
$menu   = $dependances['menu'];
$chemin = $dependances['chemin'];
$cat    = $dependances['cat'];
$dpt    = $dependances['dpt'];

require __DIR__ . '/routes/web.php';
require __DIR__ . '/routes/api.php';

$app->run();
