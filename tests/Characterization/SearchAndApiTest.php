<?php

declare(strict_types=1);

namespace Tests\Characterization;

use Tests\IntegrationTestCase;

final class SearchAndApiTest extends IntegrationTestCase
{
    public function test_search_without_filters_returns_seeded_annonces(): void
    {
        $response = $this->request('POST', '/search', [
            'motclef' => '',
            'codepostal' => '',
            'categorie' => 'Toutes catégories',
            'prix-min' => 'Min',
            'prix-max' => 'Max',
        ]);

        self::assertSame(200, $response->statusCode);
        self::assertStringContainsString("Titre de l'annonce 1", $response->body);
        self::assertStringContainsString("Titre de l'annonce 2", $response->body);
        self::assertStringContainsString("Titre de l'annonce 3", $response->body);
    }

    public function test_search_can_filter_by_city_category_and_price_range(): void
    {
        $response = $this->request('POST', '/search', [
            'motclef' => '',
            'codepostal' => 'Metz',
            'categorie' => '2',
            'prix-min' => '50',
            'prix-max' => '100',
        ]);

        self::assertSame(200, $response->statusCode);
        self::assertStringContainsString("Titre de l'annonce 2", $response->body);
        self::assertStringNotContainsString("Titre de l'annonce 1", $response->body);
        self::assertStringNotContainsString("Titre de l'annonce 3", $response->body);
    }

    public function test_api_annonce_returns_the_expected_json_payload_for_an_existing_annonce(): void
    {
        $response = $this->request('GET', '/api/annonce/1');

        self::assertSame(200, $response->statusCode);

        $payload = json_decode($response->body, true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(1, $payload['id_annonce']);
        self::assertSame("Titre de l'annonce 1", $payload['titre']);
        self::assertSame('Véhicule', $payload['categorie']['nom_categorie']);
        self::assertSame('Bernard', $payload['annonceur']['nom_annonceur']);
        self::assertSame('Meuse', $payload['departement']['nom_departement']);
    }

    public function test_api_annonce_returns_404_for_an_unknown_annonce(): void
    {
        $response = $this->request('GET', '/api/annonce/999');

        self::assertSame(404, $response->statusCode);
    }
}
