<?php
/**
 * Created by PhpStorm.
 * User: ponicorn
 * Date: 26/01/15
 * Time: 00:25
 */

namespace controller;
use model\Annonce;
use model\Annonceur;
use service\PresentateurAnnonceService;

class AnnonceurController {
    private PresentateurAnnonceService $presentateurAnnonceService;

    public function __construct(?PresentateurAnnonceService $presentateurAnnonceService = null){
        $this->presentateurAnnonceService = $presentateurAnnonceService ?? new PresentateurAnnonceService();
    }
    function afficherAnnonceur($twig, $menu, $chemin, $n, $cat) {
        $this->annonceur = annonceur::find($n);
        if(!isset($this->annonceur)){
            echo "404";
            return;
        }
        $tmp = annonce::where('id_annonceur','=',$n)->get();

        $annonces = $this->presentateurAnnonceService->presenterListe($tmp, $chemin.'/img/noimg.png', false);
        $template = $twig->load("annonceur-detail.html.twig");
        echo $template->render(array('nom' => $this->annonceur,
            "chemin" => $chemin,
            "annonces" => $annonces,
            "categories" => $cat));
    }
}
