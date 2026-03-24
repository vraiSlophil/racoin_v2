<?php

namespace controller;

use service\CleApiService;

class CleApiController {
    private CleApiService $cleApiService;

    public function __construct()
    {
        $this->cleApiService = new CleApiService();
    }

    function afficherFormulaireCle($twig, $menu, $chemin, $cat) {
        $template = $twig->load("cle-api-formulaire.html.twig");
        $menu = array(
            array('href' => $chemin,
                'text' => 'Acceuil'),
            array('href' => $chemin."/search",
                'text' => "Recherche")
        );
        echo $template->render(array("breadcrumb" => $menu, "chemin" => $chemin, "categories" => $cat));
    }

    function genererCle($twig, $menu, $chemin, $cat, $nom) {
        $resultat = $this->cleApiService->generer($nom);

        if(!$resultat['succes']) {
            $template = $twig->load("cle-api-erreur.html.twig");
            $menu = array(
                array('href' => $chemin,
                    'text' => 'Acceuil'),
                array('href' => $chemin."/search",
                    'text' => "Recherche")
            );

            echo $template->render(array("breadcrumb" => $menu, "chemin" => $chemin, "categories" => $cat));
        } else {
            $template = $twig->load("cle-api-resultat.html.twig");
            $menu = array(
                array('href' => $chemin,
                    'text' => 'Acceuil'),
                array('href' => $chemin."/search",
                    'text' => "Recherche")
            );

            // Génere clé unique de 13 caractères
            echo $template->render(array("breadcrumb" => $menu, "chemin" => $chemin, "categories" => $cat, "key" => $resultat['cle']));
        }

    }

}

?>
