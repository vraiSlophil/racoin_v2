<?php

namespace controller;

use service\AjoutAnnonceService;

class AjoutAnnonceController
{
    private AjoutAnnonceService $ajoutAnnonceService;

    public function __construct(?AjoutAnnonceService $ajoutAnnonceService = null)
    {
        $this->ajoutAnnonceService = $ajoutAnnonceService ?? new AjoutAnnonceService();
    }

    function afficherFormulaireAjout($twig, $menu, $chemin, $cat, $dpt)
    {
        $template = $twig->load("annonce-ajout-formulaire.html.twig");
        echo $template->render(array(
                "breadcrumb"   => $menu,
                "chemin"       => $chemin,
                "categories"   => $cat,
                "departements" => $dpt
            )
        );

    }

    function ajouterAnnonce($twig, $menu, $chemin, $allPostVars)
    {
        $resultat = $this->ajoutAnnonceService->ajouter($allPostVars);

        if (!$resultat['succes']) {

            $template = $twig->load("annonce-formulaire-erreurs.html.twig");
            echo $template->render(array(
                    "breadcrumb" => $menu,
                    "chemin"     => $chemin,
                    "errors"     => $resultat['erreurs']
                )
            );
            return;
        }

        $template = $twig->load("annonce-ajout-confirmation.html.twig");
        echo $template->render(array("breadcrumb" => $menu, "chemin" => $chemin));
    }
}
