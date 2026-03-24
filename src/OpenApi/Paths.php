<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

final class Paths
{
    #[OA\Get(
        path: '/api/annonce/{id}',
        operationId: 'getAnnonceById',
        summary: 'Récupère une annonce',
        tags: ['Annonces'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Identifiant de l’annonce',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', format: 'int64', minimum: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Annonce trouvée',
                content: new OA\JsonContent(ref: '#/components/schemas/AnnonceApiDetail')
            ),
            new OA\Response(
                response: 404,
                description: 'Annonce introuvable'
            ),
        ]
    )]
    public function getAnnonceById(): void {}

    #[OA\Get(
        path: '/api/annonces',
        operationId: 'listAnnonces',
        summary: 'Liste les annonces',
        tags: ['Annonces'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des annonces',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/AnnonceApiSummary')
                )
            ),
        ]
    )]
    public function listAnnonces(): void {}

    #[OA\Get(
        path: '/api/categorie/{id}',
        operationId: 'getCategorieById',
        summary: 'Récupère une catégorie et ses annonces',
        tags: ['Catégories'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Identifiant de la catégorie',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', format: 'int64', minimum: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Catégorie trouvée',
                content: new OA\JsonContent(ref: '#/components/schemas/CategorieApiDetail')
            ),
            new OA\Response(
                response: 404,
                description: 'Catégorie introuvable'
            ),
        ]
    )]
    public function getCategorieById(): void {}

    #[OA\Get(
        path: '/api/categories',
        operationId: 'listCategories',
        summary: 'Liste les catégories',
        tags: ['Catégories'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des catégories',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/CategorieApiSummary')
                )
            ),
        ]
    )]
    public function listCategories(): void {}

    #[OA\Get(
        path: '/api/key',
        operationId: 'showApiKeyForm',
        summary: 'Affiche le formulaire de génération de clé API',
        tags: ['Clés API'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Page HTML du formulaire',
                content: new OA\MediaType(
                    mediaType: 'text/html',
                    schema: new OA\Schema(type: 'string')
                )
            ),
        ]
    )]
    public function showApiKeyForm(): void {}

    #[OA\Post(
        path: '/api/key',
        operationId: 'generateApiKey',
        summary: 'Génère une clé API',
        tags: ['Clés API'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(
                    type: 'object',
                    required: ['nom'],
                    properties: [
                        new OA\Property(
                            property: 'nom',
                            type: 'string',
                            minLength: 1,
                            example: 'Nathan'
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Page HTML contenant la clé générée ou un message d’erreur',
                content: new OA\MediaType(
                    mediaType: 'text/html',
                    schema: new OA\Schema(type: 'string')
                )
            ),
        ]
    )]
    public function generateApiKey(): void {}
}
