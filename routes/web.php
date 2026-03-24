<?php

use controller\AccueilController;
use controller\AjoutAnnonceController;
use controller\AnnonceController;
use controller\AnnonceurController;
use controller\CategorieController;
use controller\RechercheController;

$app->get('/', function ($request, $response) use ($twig, $menu, $chemin, $cat) {
    $accueil = new AccueilController();
    $accueil->afficherAccueil($twig, $menu, $chemin, $cat->listerCategories());
    return $response;
});

$app->get('/item/{n}', function ($request, $response, $arg) use ($twig, $menu, $chemin, $cat) {
    $n       = $arg['n'];
    $annonce = new AnnonceController();
    $annonce->afficherAnnonce($twig, $menu, $chemin, $n, $cat->listerCategories());
    return $response;
});

$app->get('/add', function ($request, $response) use ($twig, $menu, $chemin, $cat, $dpt) {
    $ajout = new AjoutAnnonceController();
    $ajout->afficherFormulaireAjout($twig, $menu, $chemin, $cat->listerCategories(), $dpt->listerDepartements());
    return $response;
});

$app->post('/add', function ($request, $response) use ($twig, $menu, $chemin) {
    $allPostVars = $request->getParsedBody();
    $ajout       = new AjoutAnnonceController();
    $ajout->ajouterAnnonce($twig, $menu, $chemin, $allPostVars);
    return $response;
});

$app->get('/item/{id}/edit', function ($request, $response, $arg) use ($twig, $menu, $chemin) {
    $id      = $arg['id'];
    $annonce = new AnnonceController();
    $annonce->afficherAuthentificationModification($twig, $menu, $chemin, $id);
    return $response;
});

$app->post('/item/{id}/edit', function ($request, $response, $arg) use ($twig, $menu, $chemin, $cat, $dpt) {
    $id          = $arg['id'];
    $allPostVars = $request->getParsedBody();
    $annonce     = new AnnonceController();
    $annonce->afficherFormulaireModification($twig, $menu, $chemin, $id, (string) ($allPostVars['pass'] ?? ''), $cat->listerCategories(), $dpt->listerDepartements());
    return $response;
});

$app->post('/item/{id}/confirm', function ($request, $response, $arg) use ($twig, $menu, $chemin) {
    $id          = $arg['id'];
    $allPostVars = $request->getParsedBody() ?? [];
    $annonce     = new AnnonceController();
    $annonce->modifierAnnonce($twig, $menu, $chemin, $id, $allPostVars);
    return $response;
});

$app->get('/search', function ($request, $response) use ($twig, $menu, $chemin, $cat) {
    $recherche = new RechercheController();
    $recherche->afficherFormulaireRecherche($twig, $menu, $chemin, $cat->listerCategories());
    return $response;
});

$app->post('/search', function ($request, $response) use ($twig, $menu, $chemin, $cat) {
    $filtres   = $request->getParsedBody();
    $recherche = new RechercheController();
    $recherche->rechercherAnnonces($filtres, $twig, $menu, $chemin, $cat->listerCategories());
    return $response;
});

$app->get('/annonceur/{n}', function ($request, $response, $arg) use ($twig, $menu, $chemin, $cat) {
    $n         = $arg['n'];
    $annonceur = new AnnonceurController();
    $annonceur->afficherAnnonceur($twig, $menu, $chemin, $n, $cat->listerCategories());
    return $response;
});

$app->get('/del/{n}', function ($request, $response, $arg) use ($twig, $menu, $chemin) {
    $n       = $arg['n'];
    $annonce = new AnnonceController();
    $annonce->afficherFormulaireSuppression($twig, $menu, $chemin, $n);
    return $response;
});

$app->post('/del/{n}', function ($request, $response, $arg) use ($twig, $menu, $chemin, $cat) {
    $n       = $arg['n'];
    $donnees = $request->getParsedBody();
    $annonce = new AnnonceController();
    $annonce->supprimerAnnonce($twig, $menu, $chemin, $n, $cat->listerCategories(), (string) ($donnees['pass'] ?? ''));
    return $response;
});

$app->get('/cat/{n}', function ($request, $response, $arg) use ($twig, $menu, $chemin, $cat) {
    $n         = $arg['n'];
    $categorie = new CategorieController();
    $categorie->afficherCategorie($twig, $menu, $chemin, $cat->listerCategories(), $n);
    return $response;
});
