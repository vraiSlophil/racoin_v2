<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Racoin API',
    description: 'Documentation OpenAPI générée à partir du code source.'
)]
#[OA\Server(
    url: '/',
    description: 'Base URL par défaut'
)]
#[OA\Tag(
    name: 'Annonces',
    description: 'Consultation des annonces'
)]
#[OA\Tag(
    name: 'Catégories',
    description: 'Consultation des catégories'
)]
#[OA\Tag(
    name: 'Clés API',
    description: 'Génération de clé API via les écrans HTML existants'
)]
final class Metadata {}
