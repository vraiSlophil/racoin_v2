<?php

namespace controller;

use model\Annonce;
use service\PresentateurAnnonceService;

class AccueilController
{
    protected $annonce = array();
    private PresentateurAnnonceService $presentateurAnnonceService;

    public function __construct(?PresentateurAnnonceService $presentateurAnnonceService = null)
    {
        $this->presentateurAnnonceService = $presentateurAnnonceService ?? new PresentateurAnnonceService();
    }

    public function afficherAccueil($twig, $menu, $chemin, $cat)
    {
        $template = $twig->load("liste-annonces.html.twig");
        $menu     = array(
            array(
                'href' => $chemin,
                'text' => 'Acceuil'
            ),
        );

        $this->chargerAnnoncesAccueil($chemin);
        echo $template->render(array(
            "breadcrumb" => $menu,
            "chemin"     => $chemin,
            "categories" => $cat,
            "annonces"   => $this->annonce
        ));
    }

    public function chargerAnnoncesAccueil($chemin)
    {
        $tmp = Annonce::with('Annonceur')->orderBy('id_annonce', 'desc')->take(12)->get();
        $this->annonce = $this->presentateurAnnonceService->presenterListe($tmp, '/img/noimg.png');
    }
}
