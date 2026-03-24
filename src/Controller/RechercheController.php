<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\RechercheAnnonceService;
use Twig\Environment;

class RechercheController
{
    private RechercheAnnonceService $rechercheAnnonceService;

    public function __construct(?RechercheAnnonceService $rechercheAnnonceService = null)
    {
        $this->rechercheAnnonceService = $rechercheAnnonceService ?? new RechercheAnnonceService();
    }

    public function afficherFormulaireRecherche(Environment $twig, array $menu, string $chemin, array $cat): void
    {
        $template = $twig->load('recherche-formulaire.html.twig');
        $menu = [
            [
                'href' => $chemin,
                'text' => 'Acceuil',
            ],
            [
                'href' => $chemin . 'search',
                'text' => 'Recherche',
            ],
        ];

        echo $template->render([
            'breadcrumb' => $menu,
            'chemin' => $chemin,
            'categories' => $cat,
        ]);
    }

    public function rechercherAnnonces(array $array, Environment $twig, array $menu, string $chemin, array $cat): void
    {
        $template = $twig->load('liste-annonces.html.twig');
        $menu = [
            [
                'href' => $chemin,
                'text' => 'Acceuil',
            ],
            [
                'href' => $chemin . 'search',
                'text' => 'Résultats de la recherche',
            ],
        ];

        $annonces = $this->rechercheAnnonceService->rechercher($array);

        echo $template->render([
            'breadcrumb' => $menu,
            'chemin' => $chemin,
            'annonces' => $annonces,
            'categories' => $cat,
        ]);
    }
}
