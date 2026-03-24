<?php

declare(strict_types=1);

namespace controller;

use model\Annonce;
use service\PresentateurAnnonceService;
use Twig\Environment;

class AccueilController
{
    private PresentateurAnnonceService $presentateurAnnonceService;

    public function __construct(?PresentateurAnnonceService $presentateurAnnonceService = null)
    {
        $this->presentateurAnnonceService = $presentateurAnnonceService ?? new PresentateurAnnonceService();
    }

    public function afficherAccueil(Environment $twig, array $menu, string $chemin, array $cat): void
    {
        $template = $twig->load('liste-annonces.html.twig');
        $menu = [
            [
                'href' => $chemin,
                'text' => 'Acceuil',
            ],
        ];

        echo $template->render([
            'breadcrumb' => $menu,
            'chemin' => $chemin,
            'categories' => $cat,
            'annonces' => $this->chargerAnnoncesAccueil(),
        ]);
    }

    public function chargerAnnoncesAccueil(): array
    {
        $annonces = Annonce::with('Annonceur')->orderBy('id_annonce', 'desc')->take(12)->get();
        return $this->presentateurAnnonceService->presenterListe($annonces, '/img/noimg.png');
    }
}
