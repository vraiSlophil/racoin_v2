<?php

namespace controller;

use model\Annonce;
use model\Categorie;
use service\PresentateurAnnonceService;

class CategorieController {

    protected $categories = array();
    private PresentateurAnnonceService $presentateurAnnonceService;

    public function __construct(?PresentateurAnnonceService $presentateurAnnonceService = null)
    {
        $this->presentateurAnnonceService = $presentateurAnnonceService ?? new PresentateurAnnonceService();
    }

    public function listerCategories() {
        return Categorie::orderBy('nom_categorie')->get()->toArray();
    }

    public function chargerContenuCategorie($chemin, $n) {
        $tmp = Annonce::with("Annonceur")->orderBy('id_annonce','desc')->where('id_categorie', "=", $n)->get();
        $this->annonce = $this->presentateurAnnonceService->presenterListe($tmp, $chemin.'/img/noimg.png');
    }

    public function afficherCategorie($twig, $menu, $chemin, $cat, $n) {
        $template = $twig->load("liste-annonces.html.twig");
        $menu = array(
            array('href' => $chemin,
                'text' => 'Acceuil'),
            array('href' => $chemin."/cat/".$n,
                'text' => Categorie::find($n)->nom_categorie)
        );

        $this->chargerContenuCategorie($chemin, $n);
        echo $template->render(array(
            "breadcrumb" => $menu,
            "chemin" => $chemin,
            "categories" => $cat,
            "annonces" => $this->annonce));
    }
}
