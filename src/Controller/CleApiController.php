<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\CleApiService;
use Twig\Environment;

class CleApiController
{
    private CleApiService $cleApiService;

    public function __construct(?CleApiService $cleApiService = null)
    {
        $this->cleApiService = $cleApiService ?? new CleApiService();
    }

    public function afficherFormulaireCle(Environment $twig, array $menu, string $chemin, array $cat): void
    {
        $template = $twig->load('cle-api-formulaire.html.twig');
        $menu = [
            [
                'href' => $chemin,
                'text' => 'Acceuil',
            ],
            [
                'href' => $chemin . '/search',
                'text' => 'Recherche',
            ],
        ];

        echo $template->render([
            'breadcrumb' => $menu,
            'chemin' => $chemin,
            'categories' => $cat,
        ]);
    }

    public function genererCle(Environment $twig, array $menu, string $chemin, array $cat, string $nom): void
    {
        $resultat = $this->cleApiService->generer($nom);
        $templateName = $resultat['succes'] ? 'cle-api-resultat.html.twig' : 'cle-api-erreur.html.twig';
        $template = $twig->load($templateName);
        $menu = [
            [
                'href' => $chemin,
                'text' => 'Acceuil',
            ],
            [
                'href' => $chemin . '/search',
                'text' => 'Recherche',
            ],
        ];

        echo $template->render([
            'breadcrumb' => $menu,
            'chemin' => $chemin,
            'categories' => $cat,
            'key' => $resultat['cle'] ?? null,
        ]);
    }
}
