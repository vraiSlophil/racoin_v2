<?php

namespace controller;
use AllowDynamicProperties;
use model\Annonce;
use model\Annonceur;
use model\Categorie;
use model\Departement;
use model\Photo;
use service\ModificationAnnonceService;
use service\SuppressionAnnonceService;

#[AllowDynamicProperties] class AnnonceController {
    private ModificationAnnonceService $modificationAnnonceService;
    private SuppressionAnnonceService $suppressionAnnonceService;

    public function __construct(?SuppressionAnnonceService $suppressionAnnonceService = null, ?ModificationAnnonceService $modificationAnnonceService = null){
        $this->suppressionAnnonceService = $suppressionAnnonceService ?? new SuppressionAnnonceService();
        $this->modificationAnnonceService = $modificationAnnonceService ?? new ModificationAnnonceService();
    }
    function afficherAnnonce($twig, $menu, $chemin, $n, $cat): void
    {

        $this->annonce = Annonce::find($n);
        if(!isset($this->annonce)){
            echo "404";
            return;
        }

        $menu = array(
            array('href' => $chemin,
                'text' => 'Acceuil'),
            array('href' => $chemin."/cat/".$n,
                'text' => Categorie::find($this->annonce->id_categorie)?->nom_categorie),
            array('href' => $chemin."/item/".$n,
            'text' => $this->annonce->titre)
        );

        $this->annonceur = Annonceur::find($this->annonce->id_annonceur);
        $this->departement = Departement::find($this->annonce->id_departement );
        $this->photo = Photo::where('id_annonce', '=', $n)->get();
        $template = $twig->load("annonce-detail.html.twig");
        echo $template->render(array("breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $this->annonce,
            "annonceur" => $this->annonceur,
            "dep" => $this->departement->nom_departement,
            "photo" => $this->photo,
            "categories" => $cat));
    }

    function afficherFormulaireSuppression($twig, $menu, $chemin,$n){
        $this->annonce = Annonce::find($n);
        if(!isset($this->annonce)){
            echo "404";
            return;
        }
        $template = $twig->load("annonce-suppression-formulaire.html.twig");
        echo $template->render(array("breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $this->annonce));
    }


    function supprimerAnnonce($twig, $menu, $chemin, $n, $cat, $motDePasse){
        $resultat = $this->suppressionAnnonceService->supprimer($n, (string) $motDePasse);
        if (!isset($resultat['annonce'])) {
            echo "404";
            return;
        }

        $template = $twig->load("annonce-suppression-resultat.html.twig");
        echo $template->render(array("breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $resultat['annonce'],
            "pass" => $resultat['motDePasseValide'],
            "categories" => $cat));
    }

    function afficherAuthentificationModification($twig, $menu, $chemin, $id){
        $this->annonce = Annonce::find($id);
        if(!isset($this->annonce)){
            echo "404";
            return;
        }
        $template = $twig->load("annonce-modification-authentification.html.twig");
        echo $template->render(array("breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $this->annonce));
    }

    function afficherFormulaireModification($twig, $menu, $chemin, $n, $motDePasse, $cat, $dpt){
        $resultat = $this->modificationAnnonceService->chargerFormulaire($n, (string) $motDePasse);
        if (!isset($resultat['annonce'])) {
            echo "404";
            return;
        }

        $template = $twig->load("annonce-modification-formulaire.html.twig");
        echo $template->render(array("breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $resultat['annonce'],
            "annonceur" => $resultat['annonceur'],
            "pass" => $resultat['motDePasseValide'],
            "categories" => $cat,
            "departements" => $dpt,
            "dptItem" => $resultat['departementCourant'],
            "categItem" => $resultat['categorieCourante']));
    }

    function modifierAnnonce($twig, $menu, $chemin, $id, $allPostVars){
        $resultat = $this->modificationAnnonceService->modifier($id, $allPostVars);

        if ($resultat['introuvable']) {
            echo "404";
            return;
        }

        if (!$resultat['succes']) {

            $template = $twig->load("annonce-formulaire-erreurs.html.twig");
            echo $template->render(array(
                    "breadcrumb" => $menu,
                    "chemin" => $chemin,
                    "errors" => $resultat['erreurs'])
            );
            return;
        }

        $template = $twig->load("annonce-modification-confirmation.html.twig");
        echo $template->render(array("breadcrumb" => $menu, "chemin" => $chemin));
    }
}
