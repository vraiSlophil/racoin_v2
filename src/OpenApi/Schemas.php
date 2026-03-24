<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ResourceLinks',
    required: ['self'],
    properties: [
        new OA\Property(
            property: 'self',
            type: 'object',
            required: ['href'],
            properties: [
                new OA\Property(
                    property: 'href',
                    type: 'string',
                    example: '/api/annonce/1'
                ),
            ]
        ),
    ]
)]
#[OA\Schema(
    schema: 'CategorieEmbedded',
    properties: [
        new OA\Property(property: 'id_categorie', type: 'integer', format: 'int64', example: 1),
        new OA\Property(property: 'nom_categorie', type: 'string', nullable: true, example: 'Véhicule'),
    ]
)]
#[OA\Schema(
    schema: 'AnnonceurEmbedded',
    properties: [
        new OA\Property(property: 'email', type: 'string', nullable: true, example: 'bernard@example.fr'),
        new OA\Property(property: 'nom_annonceur', type: 'string', nullable: true, example: 'Bernard'),
        new OA\Property(property: 'telephone', type: 'string', nullable: true, example: '0601020304'),
    ]
)]
#[OA\Schema(
    schema: 'DepartementEmbedded',
    properties: [
        new OA\Property(property: 'id_departement', type: 'integer', format: 'int64', example: 55),
        new OA\Property(property: 'nom_departement', type: 'string', nullable: true, example: 'Meuse'),
    ]
)]
#[OA\Schema(
    schema: 'AnnonceApiSummary',
    required: ['id_annonce', 'prix', 'titre', 'ville', 'links'],
    properties: [
        new OA\Property(property: 'id_annonce', type: 'integer', format: 'int64', example: 1),
        new OA\Property(property: 'prix', type: 'number', format: 'float', nullable: true, example: 1500),
        new OA\Property(property: 'titre', type: 'string', nullable: true, example: 'Peugeot 206'),
        new OA\Property(property: 'ville', type: 'string', nullable: true, example: 'Bar-le-Duc'),
        new OA\Property(property: 'links', ref: '#/components/schemas/ResourceLinks'),
    ]
)]
#[OA\Schema(
    schema: 'AnnonceApiDetail',
    required: ['id_annonce', 'categorie', 'annonceur', 'departement', 'prix', 'date', 'titre', 'description', 'ville', 'links'],
    properties: [
        new OA\Property(property: 'id_annonce', type: 'integer', format: 'int64', example: 1),
        new OA\Property(property: 'categorie', ref: '#/components/schemas/CategorieEmbedded'),
        new OA\Property(property: 'annonceur', ref: '#/components/schemas/AnnonceurEmbedded'),
        new OA\Property(property: 'departement', ref: '#/components/schemas/DepartementEmbedded'),
        new OA\Property(property: 'prix', type: 'number', format: 'float', nullable: true, example: 1500),
        new OA\Property(property: 'date', type: 'string', format: 'date', nullable: true, example: '2017-05-14'),
        new OA\Property(property: 'titre', type: 'string', nullable: true, example: 'Peugeot 206'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Vends citadine en bon état.'),
        new OA\Property(property: 'ville', type: 'string', nullable: true, example: 'Bar-le-Duc'),
        new OA\Property(property: 'links', ref: '#/components/schemas/ResourceLinks'),
    ]
)]
#[OA\Schema(
    schema: 'CategorieApiSummary',
    required: ['id_categorie', 'nom_categorie', 'links'],
    properties: [
        new OA\Property(property: 'id_categorie', type: 'integer', format: 'int64', example: 1),
        new OA\Property(property: 'nom_categorie', type: 'string', nullable: true, example: 'Véhicule'),
        new OA\Property(property: 'links', ref: '#/components/schemas/ResourceLinks'),
    ]
)]
#[OA\Schema(
    schema: 'CategorieApiDetail',
    required: ['id_categorie', 'nom_categorie', 'links', 'annonces'],
    properties: [
        new OA\Property(property: 'id_categorie', type: 'integer', format: 'int64', example: 1),
        new OA\Property(property: 'nom_categorie', type: 'string', nullable: true, example: 'Véhicule'),
        new OA\Property(property: 'links', ref: '#/components/schemas/ResourceLinks'),
        new OA\Property(
            property: 'annonces',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/AnnonceApiSummary')
        ),
    ]
)]
final class Schemas {}
