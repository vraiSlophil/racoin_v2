<?php

declare(strict_types=1);

use controller\AccueilController;
use controller\AjoutAnnonceController;
use controller\AnnonceController;
use controller\AnnonceurController;
use controller\CategorieController;
use controller\RechercheController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response) use ($twig, $menu, $chemin, $cat): ResponseInterface {
    $accueil = new AccueilController();
    $accueil->afficherAccueil($twig, $menu, $chemin, $cat->listerCategories());
    return $response;
});

$app->get('/item/{n}', function (ServerRequestInterface $request, ResponseInterface $response, array $arg) use ($twig, $menu, $chemin, $cat): ResponseInterface {
    $n = $arg['n'];
    $annonce = new AnnonceController();
    $annonce->afficherAnnonce($twig, $menu, $chemin, $n, $cat->listerCategories());
    return $response;
});

$app->get('/add', function (ServerRequestInterface $request, ResponseInterface $response) use ($twig, $menu, $chemin, $cat, $dpt): ResponseInterface {
    $ajout = new AjoutAnnonceController();
    $ajout->afficherFormulaireAjout($twig, $menu, $chemin, $cat->listerCategories(), $dpt->listerDepartements());
    return $response;
});

$app->post('/add', function (ServerRequestInterface $request, ResponseInterface $response) use ($twig, $menu, $chemin): ResponseInterface {
    $allPostVars = $request->getParsedBody();
    $allPostVars = is_array($allPostVars) ? $allPostVars : [];
    $ajout = new AjoutAnnonceController();
    $ajout->ajouterAnnonce($twig, $menu, $chemin, $allPostVars);
    return $response;
});

$app->get('/item/{id}/edit', function (ServerRequestInterface $request, ResponseInterface $response, array $arg) use ($twig, $menu, $chemin): ResponseInterface {
    $id = $arg['id'];
    $annonce = new AnnonceController();
    $annonce->afficherAuthentificationModification($twig, $menu, $chemin, $id);
    return $response;
});

$app->post('/item/{id}/edit', function (ServerRequestInterface $request, ResponseInterface $response, array $arg) use ($twig, $menu, $chemin, $cat, $dpt): ResponseInterface {
    $id = $arg['id'];
    $allPostVars = $request->getParsedBody();
    $allPostVars = is_array($allPostVars) ? $allPostVars : [];
    $annonce = new AnnonceController();
    $annonce->afficherFormulaireModification($twig, $menu, $chemin, $id, (string) ($allPostVars['pass'] ?? ''), $cat->listerCategories(), $dpt->listerDepartements());
    return $response;
});

$app->post('/item/{id}/confirm', function (ServerRequestInterface $request, ResponseInterface $response, array $arg) use ($twig, $menu, $chemin): ResponseInterface {
    $id = $arg['id'];
    $allPostVars = $request->getParsedBody();
    $allPostVars = is_array($allPostVars) ? $allPostVars : [];
    $annonce = new AnnonceController();
    $annonce->modifierAnnonce($twig, $menu, $chemin, $id, $allPostVars);
    return $response;
});

$app->get('/search', function (ServerRequestInterface $request, ResponseInterface $response) use ($twig, $menu, $chemin, $cat): ResponseInterface {
    $recherche = new RechercheController();
    $recherche->afficherFormulaireRecherche($twig, $menu, $chemin, $cat->listerCategories());
    return $response;
});

$app->post('/search', function (ServerRequestInterface $request, ResponseInterface $response) use ($twig, $menu, $chemin, $cat): ResponseInterface {
    $filtres = $request->getParsedBody();
    $filtres = is_array($filtres) ? $filtres : [];
    $recherche = new RechercheController();
    $recherche->rechercherAnnonces($filtres, $twig, $menu, $chemin, $cat->listerCategories());
    return $response;
});

$app->get('/annonceur/{n}', function (ServerRequestInterface $request, ResponseInterface $response, array $arg) use ($twig, $menu, $chemin, $cat): ResponseInterface {
    $n = $arg['n'];
    $annonceur = new AnnonceurController();
    $annonceur->afficherAnnonceur($twig, $menu, $chemin, $n, $cat->listerCategories());
    return $response;
});

$app->get('/del/{n}', function (ServerRequestInterface $request, ResponseInterface $response, array $arg) use ($twig, $menu, $chemin): ResponseInterface {
    $n = $arg['n'];
    $annonce = new AnnonceController();
    $annonce->afficherFormulaireSuppression($twig, $menu, $chemin, $n);
    return $response;
});

$app->post('/del/{n}', function (ServerRequestInterface $request, ResponseInterface $response, array $arg) use ($twig, $menu, $chemin, $cat): ResponseInterface {
    $n = $arg['n'];
    $donnees = $request->getParsedBody();
    $donnees = is_array($donnees) ? $donnees : [];
    $annonce = new AnnonceController();
    $annonce->supprimerAnnonce($twig, $menu, $chemin, $n, $cat->listerCategories(), (string) ($donnees['pass'] ?? ''));
    return $response;
});

$app->get('/cat/{n}', function (ServerRequestInterface $request, ResponseInterface $response, array $arg) use ($twig, $menu, $chemin, $cat): ResponseInterface {
    $n = $arg['n'];
    $categorie = new CategorieController();
    $categorie->afficherCategorie($twig, $menu, $chemin, $cat->listerCategories(), $n);
    return $response;
});
