<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Controller\CategorieController;
use App\Controller\DepartementController;
use App\Db\Connection;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Middleware\OutputBufferingMiddleware;
use Slim\Psr7\Factory\StreamFactory;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

Connection::createConn();

$app = AppFactory::create();

$basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));

if ($basePath === '/' || $basePath === '.') {
    $basePath = '';
}

if ($basePath !== '') {
    $app->setBasePath($basePath);
}

$loader = new FilesystemLoader(__DIR__ . '/../template');
$twig = new Environment($loader, [
    'autoescape' => 'html',
]);

$responseFactory = $app->getResponseFactory();

$httpLogger = new Logger('http');

$handler = new StreamHandler('php://stderr', Level::Info);
$handler->setFormatter(new LineFormatter(
    "[%datetime%] %channel%.%level_name%: %message%\n",
    'Y-m-d H:i:sP',
    true,
    true
));

$httpLogger->pushHandler($handler);
$httpLogger->pushProcessor(new PsrLogMessageProcessor());


// Preserve legacy controllers that still render via echo while routes now return PSR-7 responses.
$app->add(new OutputBufferingMiddleware(new StreamFactory(), OutputBufferingMiddleware::APPEND));

$app->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler) use ($responseFactory) {
    $uri = $request->getUri();
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

$app->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler) use ($httpLogger): ResponseInterface {
    $startedAt = microtime(true);
    $response = $handler->handle($request);

    $uri = $request->getUri();
    $target = $uri->getPath();
    if ($uri->getQuery() !== '') {
        $target .= '?' . $uri->getQuery();
    }

    $clientIp = (string) ($request->getServerParams()['REMOTE_ADDR'] ?? 'unknown');

    $httpLogger->info(
        '{ip} {method} {target} -> {status} ({duration_ms} ms)',
        [
            'ip' => $clientIp,
            'method' => $request->getMethod(),
            'target' => $target,
            'status' => $response->getStatusCode(),
            'duration_ms' => number_format((microtime(true) - $startedAt) * 1000, 1, '.', ''),
        ]
    );

    return $response;
});

$chemin = $basePath === '' ? '/' : $basePath . '/';

$menu = [
    [
        'href' => $chemin,
        'text' => 'Accueil',
    ],
];

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
