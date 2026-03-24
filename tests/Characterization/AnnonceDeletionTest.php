<?php

declare(strict_types=1);

namespace Tests\Characterization;

use Tests\IntegrationTestCase;

final class AnnonceDeletionTest extends IntegrationTestCase
{
    public function test_delete_rejects_a_wrong_password_and_keeps_the_annonce(): void
    {
        $annonceId = $this->createAnnonce();

        $response = $this->request('POST', sprintf('/del/%d', $annonceId), [
            'pass' => 'mot-de-passe-faux',
        ]);

        self::assertSame(200, $response->statusCode);
        self::assertStringContainsString('mot de passe incorrecte', $response->body);
        self::assertSame(1, (int) $this->fetchScalar('SELECT COUNT(*) FROM annonce WHERE id_annonce = ?', 'i', $annonceId));
    }

    public function test_delete_accepts_the_original_password_and_removes_the_annonce(): void
    {
        $annonceId = $this->createAnnonce();

        $response = $this->request('POST', sprintf('/del/%d', $annonceId), [
            'pass' => 'secret-refactor',
        ]);

        self::assertSame(200, $response->statusCode);
        self::assertStringContainsString('Vous allez être redirigé.', $response->body);
        self::assertSame(0, (int) $this->fetchScalar('SELECT COUNT(*) FROM annonce WHERE id_annonce = ?', 'i', $annonceId));
    }

    private function createAnnonce(): int
    {
        $response = $this->request('POST', '/add', [
            'nom' => 'Alice Martin',
            'email' => 'alice@example.fr',
            'phone' => '0601020304',
            'ville' => 'Nancy',
            'departement' => '4',
            'categorie' => '1',
            'title' => 'Annonce a supprimer',
            'description' => 'Description a supprimer',
            'price' => '42',
            'psw' => 'secret-refactor',
            'confirm-psw' => 'secret-refactor',
        ]);

        self::assertSame(200, $response->statusCode);
        self::assertStringContainsString('Merci, votre annonce a bien été ajoutée.', $response->body);

        return (int) $this->fetchScalar('SELECT MAX(id_annonce) FROM annonce');
    }
}
