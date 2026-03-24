<?php

namespace controller;

use service\RechercheAnnonceService;

class RechercheController {
    private RechercheAnnonceService $rechercheAnnonceService;

    public function __construct()
    {
        $this->rechercheAnnonceService = new RechercheAnnonceService();
    }

    function afficherFormulaireRecherche($twig, $menu, $chemin, $cat) {
        $template = $twig->load("recherche-formulaire.html.twig");
        $menu = array(
            array('href' => $chemin,
                'text' => 'Acceuil'),
            array('href' => $chemin."/search",
                'text' => "Recherche")
        );
        echo $template->render(array("breadcrumb" => $menu, "chemin" => $chemin, "categories" => $cat));
    }

    function rechercherAnnonces($array, $twig, $menu, $chemin, $cat) {
        $template = $twig->load("liste-annonces.html.twig");
        $menu = array(
            array('href' => $chemin,
                'text' => 'Acceuil'),
            array('href' => $chemin."/search",
                'text' => "Résultats de la recherche")
        );

        $annonce = $this->rechercheAnnonceService->rechercher($array);

        echo $template->render(array("breadcrumb" => $menu, "chemin" => $chemin, "annonces" => $annonce, "categories" => $cat));

    }

}

?>
