<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Annonce;
use App\Model\Categorie;
use App\Service\PresentateurAnnonceService;
use Twig\Environment;

class CategorieController
{
    private PresentateurAnnonceService $presentateurAnnonceService;

    public function __construct(?PresentateurAnnonceService $presentateurAnnonceService = null)
    {
        $this->presentateurAnnonceService = $presentateurAnnonceService ?? new PresentateurAnnonceService();
    }

    public function listerCategories(): array
    {
        return Categorie::orderBy('nom_categorie')->get()->toArray();
    }

    public function chargerContenuCategorie(string $chemin, int|string $n): array
    {
        $annonces = Annonce::with('Annonceur')
            ->orderBy('id_annonce', 'desc')
            ->where('id_categorie', '=', $n)
            ->get();

        return $this->presentateurAnnonceService->presenterListe($annonces, $chemin . '/img/noimg.png');
    }

    public function afficherCategorie(Environment $twig, array $menu, string $chemin, array $cat, int|string $n): void
    {
        $template = $twig->load('liste-annonces.html.twig');
        $menu = [
            [
                'href' => $chemin,
                'text' => 'Acceuil',
            ],
            [
                'href' => $chemin . '/cat/' . $n,
                'text' => Categorie::find($n)?->nom_categorie,
            ],
        ];

        echo $template->render([
            'breadcrumb' => $menu,
            'chemin' => $chemin,
            'categories' => $cat,
            'annonces' => $this->chargerContenuCategorie($chemin, $n),
        ]);
    }
}
