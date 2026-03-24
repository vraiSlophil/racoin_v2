<?php

declare(strict_types=1);

namespace Tests\Characterization;

use Tests\IntegrationTestCase;

final class AnnonceModificationTest extends IntegrationTestCase
{
    public function test_edit_page_displays_the_password_authentication_form(): void
    {
        $annonceId = $this->createAnnonce();

        $response = $this->request('GET', sprintf('/item/%d/edit', $annonceId));

        self::assertSame(200, $response->statusCode);
        self::assertStringContainsString("Mot de passe pour modifier l'annonce", $response->body);
        self::assertStringContainsString('Annonce a modifier', $response->body);
    }

    public function test_edit_rejects_a_wrong_password_and_does_not_show_the_modification_form(): void
    {
        $annonceId = $this->createAnnonce();

        $response = $this->request('POST', sprintf('/item/%d/edit', $annonceId), [
            'pass' => 'mot-de-passe-faux',
        ]);

        self::assertSame(200, $response->statusCode);
        self::assertStringContainsString('Mot de passe incorrect', $response->body);
        self::assertStringNotContainsString('Modifier mon annonce', $response->body);
    }

    public function test_edit_accepts_the_original_password_and_updates_the_annonce_with_sanitized_values(): void
    {
        $annonceId = $this->createAnnonce();

        $authenticationResponse = $this->request('POST', sprintf('/item/%d/edit', $annonceId), [
            'pass' => 'secret-refactor',
        ]);

        self::assertSame(200, $authenticationResponse->statusCode);
        self::assertStringContainsString('Modifier mon annonce', $authenticationResponse->body);
        self::assertStringContainsString('Annonce a modifier', $authenticationResponse->body);

        $response = $this->request('POST', sprintf('/item/%d/confirm', $annonceId), [
            'nom' => 'Alice <b>Modifiee</b>',
            'email' => 'alice.modifiee@example.fr',
            'phone' => '0611223344',
            'ville' => 'Metz',
            'departement' => '3',
            'categorie' => '2',
            'title' => 'Titre <script>update()</script>',
            'description' => 'Description <b>mise a jour</b>',
            'price' => '84.5',
        ]);

        self::assertSame(200, $response->statusCode);
        self::assertStringContainsString('Votre annonce a bien été modifier.', $response->body);

        $updatedAnnonce = $this->fetchOne(
            'SELECT a.titre, a.description, a.ville, a.prix, a.mdp, a.id_departement, a.id_categorie, an.nom_annonceur, an.email, an.telephone
             FROM annonce a
             INNER JOIN annonceur an ON an.id_annonceur = a.id_annonceur
             WHERE a.id_annonce = ?',
            'i',
            $annonceId
        );

        self::assertNotNull($updatedAnnonce);
        self::assertSame('Alice &lt;b&gt;Modifiee&lt;/b&gt;', $updatedAnnonce['nom_annonceur']);
        self::assertSame('Titre &lt;script&gt;update()&lt;/script&gt;', $updatedAnnonce['titre']);
        self::assertSame('Description &lt;b&gt;mise a jour&lt;/b&gt;', $updatedAnnonce['description']);
        self::assertSame('Metz', $updatedAnnonce['ville']);
        self::assertSame('84.5', (string) $updatedAnnonce['prix']);
        self::assertSame('3', (string) $updatedAnnonce['id_departement']);
        self::assertSame('2', (string) $updatedAnnonce['id_categorie']);
        self::assertSame('alice.modifiee@example.fr', $updatedAnnonce['email']);
        self::assertSame('0611223344', $updatedAnnonce['telephone']);
        self::assertTrue(password_verify('secret-refactor', $updatedAnnonce['mdp']));
    }

    public function test_edit_rejects_an_invalid_payload_without_updating_the_annonce(): void
    {
        $annonceId = $this->createAnnonce();

        $response = $this->request('POST', sprintf('/item/%d/confirm', $annonceId), [
            'nom' => '',
            'email' => 'adresse-invalide',
            'phone' => '0601020304',
            'ville' => 'Nancy',
            'departement' => '4',
            'categorie' => '1',
            'title' => 'Titre invalide',
            'description' => 'Description invalide',
            'price' => '42',
        ]);

        self::assertSame(200, $response->statusCode);
        self::assertStringContainsString('Veuillez entrer votre nom', $response->body);
        self::assertStringContainsString('Veuillez entrer une adresse mail correcte', $response->body);

        $storedAnnonce = $this->fetchOne(
            'SELECT a.titre, a.description, an.nom_annonceur, an.email
             FROM annonce a
             INNER JOIN annonceur an ON an.id_annonceur = a.id_annonceur
             WHERE a.id_annonce = ?',
            'i',
            $annonceId
        );

        self::assertNotNull($storedAnnonce);
        self::assertSame('Annonce a modifier', $storedAnnonce['titre']);
        self::assertSame('Description a modifier', $storedAnnonce['description']);
        self::assertSame('Alice Martin', $storedAnnonce['nom_annonceur']);
        self::assertSame('alice@example.fr', $storedAnnonce['email']);
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
            'title' => 'Annonce a modifier',
            'description' => 'Description a modifier',
            'price' => '42',
            'psw' => 'secret-refactor',
            'confirm-psw' => 'secret-refactor',
        ]);

        self::assertSame(200, $response->statusCode);
        self::assertStringContainsString('Merci, votre annonce a bien été ajoutée.', $response->body);

        return (int) $this->fetchScalar('SELECT MAX(id_annonce) FROM annonce');
    }
}
