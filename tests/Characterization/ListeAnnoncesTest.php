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
        self::assertStringContainsString('src="/img/noimg.png"', $response->body);
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

    public function test_annonce_page_uses_root_relative_links_for_actions_and_assets(): void
    {
        $response = $this->request('GET', '/item/1');

        self::assertSame(200, $response->statusCode);
        self::assertStringContainsString('href="/">Acceuil</a>', $response->body);
        self::assertStringContainsString('src="/js/jquery-1.11.1.min.js"', $response->body);
        self::assertStringContainsString('src="/js/annonce.js"', $response->body);
        self::assertStringContainsString('src="/img/email2.png"', $response->body);
        self::assertStringContainsString('src="/img/bin.png"', $response->body);
        self::assertStringContainsString('src="/img/edit.png"', $response->body);
        self::assertStringNotContainsString('./index.php', $response->body);
        self::assertStringNotContainsString('../js/', $response->body);
        self::assertStringNotContainsString('../img/', $response->body);
    }

    public function test_compiled_stylesheet_is_served_from_the_public_directory(): void
    {
        $response = $this->request('GET', '/stylesheets/master.css');

        self::assertSame(200, $response->statusCode);
        self::assertStringContainsString('body', $response->body);
    }
}
