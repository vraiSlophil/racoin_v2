<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Annonce;
use App\Model\Annonceur;
use App\Model\Categorie;
use App\Model\Departement;
use App\Model\Photo;
use App\Service\ModificationAnnonceService;
use App\Service\SuppressionAnnonceService;
use Twig\Environment;

class AnnonceController
{
    private ModificationAnnonceService $modificationAnnonceService;
    private SuppressionAnnonceService $suppressionAnnonceService;

    public function __construct(
        ?SuppressionAnnonceService $suppressionAnnonceService = null,
        ?ModificationAnnonceService $modificationAnnonceService = null,
    ) {
        $this->suppressionAnnonceService = $suppressionAnnonceService ?? new SuppressionAnnonceService();
        $this->modificationAnnonceService = $modificationAnnonceService ?? new ModificationAnnonceService();
    }

    public function afficherAnnonce(Environment $twig, array $menu, string $chemin, int|string $n, array $cat): void
    {
        $annonce = Annonce::find($n);
        if (!isset($annonce)) {
            $this->renderNotFound();
            return;
        }

        $menu = [
            [
                'href' => $chemin,
                'text' => 'Acceuil',
            ],
            [
                'href' => $chemin . '/cat/' . $n,
                'text' => Categorie::find($annonce->id_categorie)?->nom_categorie,
            ],
            [
                'href' => $chemin . '/item/' . $n,
                'text' => $annonce->titre,
            ],
        ];

        $annonceur = Annonceur::find($annonce->id_annonceur);
        $departement = Departement::find($annonce->id_departement);
        $photos = Photo::where('id_annonce', '=', $n)->get();
        $template = $twig->load('annonce-detail.html.twig');
        echo $template->render([
            'breadcrumb' => $menu,
            'chemin' => $chemin,
            'annonce' => $annonce,
            'annonceur' => $annonceur,
            'dep' => $departement?->nom_departement,
            'photo' => $photos,
            'categories' => $cat,
        ]);
    }

    public function afficherFormulaireSuppression(Environment $twig, array $menu, string $chemin, int|string $n): void
    {
        $annonce = Annonce::find($n);
        if (!isset($annonce)) {
            $this->renderNotFound();
            return;
        }
        $template = $twig->load('annonce-suppression-formulaire.html.twig');
        echo $template->render([
            'breadcrumb' => $menu,
            'chemin' => $chemin,
            'annonce' => $annonce,
        ]);
    }

    public function supprimerAnnonce(
        Environment $twig,
        array $menu,
        string $chemin,
        int|string $n,
        array $cat,
        string $motDePasse,
    ): void {
        $resultat = $this->suppressionAnnonceService->supprimer($n, (string) $motDePasse);
        if (!isset($resultat['annonce'])) {
            $this->renderNotFound();
            return;
        }

        $template = $twig->load('annonce-suppression-resultat.html.twig');
        echo $template->render([
            'breadcrumb' => $menu,
            'chemin' => $chemin,
            'annonce' => $resultat['annonce'],
            'pass' => $resultat['motDePasseValide'],
            'categories' => $cat,
        ]);
    }

    public function afficherAuthentificationModification(
        Environment $twig,
        array $menu,
        string $chemin,
        int|string $id,
    ): void {
        $annonce = Annonce::find($id);
        if (!isset($annonce)) {
            $this->renderNotFound();
            return;
        }
        $template = $twig->load('annonce-modification-authentification.html.twig');
        echo $template->render([
            'breadcrumb' => $menu,
            'chemin' => $chemin,
            'annonce' => $annonce,
        ]);
    }

    public function afficherFormulaireModification(
        Environment $twig,
        array $menu,
        string $chemin,
        int|string $n,
        string $motDePasse,
        array $cat,
        array $dpt,
    ): void {
        $resultat = $this->modificationAnnonceService->chargerFormulaire($n, (string) $motDePasse);
        if (!isset($resultat['annonce'])) {
            $this->renderNotFound();
            return;
        }

        $template = $twig->load('annonce-modification-formulaire.html.twig');
        echo $template->render([
            'breadcrumb' => $menu,
            'chemin' => $chemin,
            'annonce' => $resultat['annonce'],
            'annonceur' => $resultat['annonceur'],
            'pass' => $resultat['motDePasseValide'],
            'categories' => $cat,
            'departements' => $dpt,
            'dptItem' => $resultat['departementCourant'],
            'categItem' => $resultat['categorieCourante'],
        ]);
    }

    public function modifierAnnonce(Environment $twig, array $menu, string $chemin, int|string $id, array $allPostVars): void
    {
        $resultat = $this->modificationAnnonceService->modifier($id, $allPostVars);

        if ($resultat['introuvable']) {
            $this->renderNotFound();
            return;
        }

        if (!$resultat['succes']) {
            $template = $twig->load('annonce-formulaire-erreurs.html.twig');
            echo $template->render([
                'breadcrumb' => $menu,
                'chemin' => $chemin,
                'errors' => $resultat['erreurs'],
            ]);
            return;
        }

        $template = $twig->load('annonce-modification-confirmation.html.twig');
        echo $template->render(['breadcrumb' => $menu, 'chemin' => $chemin]);
    }

    private function renderNotFound(): void
    {
        echo '404';
    }
}
