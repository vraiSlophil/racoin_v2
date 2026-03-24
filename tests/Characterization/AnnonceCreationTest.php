<?php

declare(strict_types=1);

namespace Tests\Characterization;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\IntegrationTestCase;

final class AnnonceCreationTest extends IntegrationTestCase
{
    public function test_add_accepts_a_valid_payload_and_persists_raw_values_while_escaping_html_at_render_time(): void
    {
        $payload = $this->validPayload();

        $response = $this->request('POST', '/add', $payload);

        self::assertSame(200, $response->statusCode);
        self::assertStringContainsString('Merci, votre annonce a bien été ajoutée.', $response->body);
        self::assertSame(4, (int) $this->fetchScalar('SELECT COUNT(*) FROM annonce'));
        self::assertSame(4, (int) $this->fetchScalar('SELECT COUNT(*) FROM annonceur'));

        $createdAnnonce = $this->fetchOne(
            'SELECT a.id_annonce, a.titre, a.description, a.ville, a.prix, a.mdp, an.nom_annonceur, an.email, an.telephone
             FROM annonce a
             INNER JOIN annonceur an ON an.id_annonceur = a.id_annonceur
             ORDER BY a.id_annonce DESC
             LIMIT 1'
        );

        self::assertNotNull($createdAnnonce);
        self::assertSame('Alice <b>Martin</b>', $createdAnnonce['nom_annonceur']);
        self::assertSame('Velo <script>alert(1)</script>', $createdAnnonce['titre']);
        self::assertSame('Description <b>detaillee</b>', $createdAnnonce['description']);
        self::assertSame('Nancy', $createdAnnonce['ville']);
        self::assertSame('123.45', (string) $createdAnnonce['prix']);
        self::assertNotSame($payload['psw'], $createdAnnonce['mdp']);
        self::assertTrue(password_verify($payload['psw'], $createdAnnonce['mdp']));

        $detailResponse = $this->request('GET', sprintf('/item/%d', $createdAnnonce['id_annonce']));

        self::assertSame(200, $detailResponse->statusCode);
        self::assertStringContainsString('Velo &lt;script&gt;alert(1)&lt;/script&gt;', $detailResponse->body);
        self::assertStringContainsString('Description &lt;b&gt;detaillee&lt;/b&gt;', $detailResponse->body);
        self::assertStringNotContainsString('Velo <script>alert(1)</script>', $detailResponse->body);
    }

    #[DataProvider('invalidPayloadProvider')]
    public function test_add_rejects_invalid_payloads_without_persisting(string $expectedMessage, array $overrides): void
    {
        $response = $this->request('POST', '/add', array_replace($this->validPayload(), $overrides));

        self::assertSame(200, $response->statusCode);
        self::assertStringContainsString($expectedMessage, $response->body);
        self::assertSame(3, (int) $this->fetchScalar('SELECT COUNT(*) FROM annonce'));
        self::assertSame(3, (int) $this->fetchScalar('SELECT COUNT(*) FROM annonceur'));
    }

    public static function invalidPayloadProvider(): array
    {
        return [
            'nom manquant' => [
                'Veuillez entrer votre nom',
                ['nom' => ''],
            ],
            'email invalide' => [
                'Veuillez entrer une adresse mail correcte',
                ['email' => 'not-an-email'],
            ],
            'departement non numerique' => [
                'Veuillez choisir un département',
                ['departement' => '-----'],
            ],
            'categorie non numerique' => [
                'Veuillez choisir une catégorie',
                ['categorie' => '-----'],
            ],
            'prix zero refuse actuellement' => [
                'Veuillez entrer un prix',
                ['price' => '0'],
            ],
            'mots de passe differents' => [
                'Les mots de passes ne sont pas identiques',
                ['confirm-psw' => 'different-secret'],
            ],
        ];
    }

    private function validPayload(): array
    {
        return [
            'nom' => 'Alice <b>Martin</b>',
            'email' => 'alice@example.fr',
            'phone' => '0601020304',
            'ville' => 'Nancy',
            'departement' => '4',
            'categorie' => '1',
            'title' => 'Velo <script>alert(1)</script>',
            'description' => 'Description <b>detaillee</b>',
            'price' => '123.45',
            'psw' => 'secret-refactor',
            'confirm-psw' => 'secret-refactor',
        ];
    }
}
