<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use controller\CategorieController;
use controller\DepartementController;
use db\connection;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Middleware\OutputBufferingMiddleware;
use Slim\Psr7\Factory\StreamFactory;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

connection::createConn();

$app = AppFactory::create();

$loader = new FilesystemLoader(__DIR__ . '/../template');
$twig   = new Environment($loader, [
    'autoescape' => 'html',
]);

$responseFactory = $app->getResponseFactory();

// Preserve legacy controllers that still render via echo while routes now return PSR-7 responses.
$app->add(new OutputBufferingMiddleware(new StreamFactory(), OutputBufferingMiddleware::APPEND));

$app->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler) use ($responseFactory) {
    $uri  = $request->getUri();
    $path = $uri->getPath();

    if ($path != '/' && str_ends_with($path, '/')) {
        $uri = $uri->withPath(substr($path, 0, -1));

        if ($request->getMethod() == 'GET') {
            return $responseFactory
                ->createResponse(301)
                ->withHeader('Location', (string) $uri);
        }

        return $handler->handle($request->withUri($uri));
    }

    return $handler->handle($request);
});

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$menu = [
    [
        'href' => './index.php',
        'text' => 'Accueil',
    ],
];

$chemin = dirname($_SERVER['SCRIPT_NAME']);

$cat = new CategorieController();
$dpt = new DepartementController();

return [
    'app' => $app,
    'twig' => $twig,
    'menu' => $menu,
    'chemin' => $chemin,
    'cat' => $cat,
    'dpt' => $dpt,
];
