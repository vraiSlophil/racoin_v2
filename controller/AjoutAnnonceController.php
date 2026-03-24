<?php

declare(strict_types=1);

namespace controller;

use service\AjoutAnnonceService;
use Twig\Environment;

class AjoutAnnonceController
{
    private AjoutAnnonceService $ajoutAnnonceService;

    public function __construct(?AjoutAnnonceService $ajoutAnnonceService = null)
    {
        $this->ajoutAnnonceService = $ajoutAnnonceService ?? new AjoutAnnonceService();
    }

    public function afficherFormulaireAjout(Environment $twig, array $menu, string $chemin, array $cat, array $dpt): void
    {
        $template = $twig->load('annonce-ajout-formulaire.html.twig');
        echo $template->render([
            'breadcrumb' => $menu,
            'chemin' => $chemin,
            'categories' => $cat,
            'departements' => $dpt,
        ]);
    }

    public function ajouterAnnonce(Environment $twig, array $menu, string $chemin, array $allPostVars): void
    {
        $resultat = $this->ajoutAnnonceService->ajouter($allPostVars);

        if (!$resultat['succes']) {
            $template = $twig->load('annonce-formulaire-erreurs.html.twig');
            echo $template->render([
                'breadcrumb' => $menu,
                'chemin' => $chemin,
                'errors' => $resultat['erreurs'],
            ]);
            return;
        }

        $template = $twig->load('annonce-ajout-confirmation.html.twig');
        echo $template->render(['breadcrumb' => $menu, 'chemin' => $chemin]);
    }
}
