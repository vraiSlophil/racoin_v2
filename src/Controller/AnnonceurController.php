<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Annonce;
use App\Model\Annonceur;
use App\Service\PresentateurAnnonceService;
use Twig\Environment;

class AnnonceurController
{
    private PresentateurAnnonceService $presentateurAnnonceService;

    public function __construct(?PresentateurAnnonceService $presentateurAnnonceService = null)
    {
        $this->presentateurAnnonceService = $presentateurAnnonceService ?? new PresentateurAnnonceService();
    }

    public function afficherAnnonceur(Environment $twig, array $menu, string $chemin, int|string $n, array $cat): void
    {
        $annonceur = Annonceur::find($n);
        if (!isset($annonceur)) {
            echo '404';
            return;
        }

        $annonces = Annonce::where('id_annonceur', '=', $n)->get();
        $annoncesPresentees = $this->presentateurAnnonceService->presenterListe($annonces, $chemin . '/img/noimg.png', false);

        $template = $twig->load('annonceur-detail.html.twig');
        echo $template->render([
            'nom' => $annonceur,
            'chemin' => $chemin,
            'annonces' => $annoncesPresentees,
            'categories' => $cat,
        ]);
    }
}
