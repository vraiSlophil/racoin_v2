<?php

declare(strict_types=1);

namespace Tests\Characterization;

use Tests\IntegrationTestCase;

final class ListeAnnoncesTest extends IntegrationTestCase
{
    public function test_home_page_shows_annonce_cards_with_annonceur_name_and_photo_metadata(): void
    {
        $response = $this->request('GET', '/');

        self::assertSame(200, $response->statusCode);
        self::assertStringContainsString('Titre de l&#039;annonce 3', $response->body);
        self::assertStringContainsString('Le 2014-12-17 par Danielle', $response->body);
        self::assertStringContainsString('0 photo(s)', $response->body);
        self::assertStringContainsString('3 photo(s)', $response->body);
        self::assertStringContainsString('src="/img/noimg.png"', $response->body);
    }

    public function test_category_page_shows_only_matching_annonces_with_the_current_default_image_path(): void
    {
        $response = $this->request('GET', '/cat/3');

        self::assertSame(200, $response->statusCode);
        self::assertStringContainsString('Titre de l&#039;annonce 3', $response->body);
        self::assertStringContainsString('Le 2014-12-17 par Danielle', $response->body);
        self::assertStringNotContainsString('Titre de l&#039;annonce 1', $response->body);
        self::assertStringNotContainsString('Titre de l&#039;annonce 2', $response->body);
        self::assertStringContainsString('src="//img/noimg.png"', $response->body);
    }

    public function test_advertiser_page_shows_contact_information_and_annonce_cards_with_photo_metadata(): void
    {
        $response = $this->request('GET', '/annonceur/1');

        self::assertSame(200, $response->statusCode);
        self::assertStringContainsString('Bernard', $response->body);
        self::assertStringContainsString('annonceur1@exemple.ptdr', $response->body);
        self::assertStringContainsString('0607080910', $response->body);
        self::assertStringContainsString('Titre de l&#039;annonce 1', $response->body);
        self::assertStringContainsString('2 photo(s)', $response->body);
        self::assertStringContainsString('http://www.routard.com/images_contenu/communaute/Photos/publi/029/pt28199.jpg', $response->body);
    }
}
