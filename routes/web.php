<?php

use controller\index as ControleurAccueil;
use controller\item as ControleurAnnonce;

$app->get('/', function () use ($twig, $menu, $chemin, $cat) {
    $index = new ControleurAccueil();
    $index->displayAllAnnonce($twig, $menu, $chemin, $cat->getCategories());
});

$app->get('/item/{n}', function ($request, $response, $arg) use ($twig, $menu, $chemin, $cat) {
    $n    = $arg['n'];
    $item = new ControleurAnnonce();
    $item->afficherItem($twig, $menu, $chemin, $n, $cat->getCategories());
});

$app->get('/add', function () use ($twig, $menu, $chemin, $cat, $dpt) {
    $ajout = new controller\addItem();
    $ajout->addItemView($twig, $menu, $chemin, $cat->getCategories(), $dpt->getAllDepartments());
});

$app->post('/add', function ($request) use ($twig, $menu, $chemin) {
    $allPostVars = $request->getParsedBody();
    $ajout       = new controller\addItem();
    $ajout->addNewItem($twig, $menu, $chemin, $allPostVars);
});

$app->get('/item/{id}/edit', function ($request, $response, $arg) use ($twig, $menu, $chemin) {
    $id   = $arg['id'];
    $item = new ControleurAnnonce();
    $item->modifyGet($twig, $menu, $chemin, $id);
});

$app->post('/item/{id}/edit', function ($request, $response, $arg) use ($twig, $menu, $chemin, $cat, $dpt) {
    $id          = $arg['id'];
    $allPostVars = $request->getParsedBody();
    $item        = new ControleurAnnonce();
    $item->modifyPost($twig, $menu, $chemin, $id, $allPostVars, $cat->getCategories(), $dpt->getAllDepartments());
});

$app->map(['GET, POST'], '/item/{id}/confirm', function ($request, $response, $arg) use ($twig, $menu, $chemin) {
    $id          = $arg['id'];
    $allPostVars = $request->getParsedBody();
    $item        = new ControleurAnnonce();
    $item->edit($twig, $menu, $chemin, $id, $allPostVars);
});

$app->get('/search', function () use ($twig, $menu, $chemin, $cat) {
    $recherche = new controller\Search();
    $recherche->show($twig, $menu, $chemin, $cat->getCategories());
});

$app->post('/search', function ($request, $response) use ($twig, $menu, $chemin, $cat) {
    $filtres   = $request->getParsedBody();
    $recherche = new controller\Search();
    $recherche->research($filtres, $twig, $menu, $chemin, $cat->getCategories());
});

$app->get('/annonceur/{n}', function ($request, $response, $arg) use ($twig, $menu, $chemin, $cat) {
    $n         = $arg['n'];
    $annonceur = new controller\viewAnnonceur();
    $annonceur->afficherAnnonceur($twig, $menu, $chemin, $n, $cat->getCategories());
});

$app->get('/del/{n}', function ($request, $response, $arg) use ($twig, $menu, $chemin) {
    $n    = $arg['n'];
    $item = new ControleurAnnonce();
    $item->supprimerItemGet($twig, $menu, $chemin, $n);
});

$app->post('/del/{n}', function ($request, $response, $arg) use ($twig, $menu, $chemin, $cat) {
    $n    = $arg['n'];
    $item = new ControleurAnnonce();
    $item->supprimerItemPost($twig, $menu, $chemin, $n, $cat->getCategories());
});

$app->get('/cat/{n}', function ($request, $response, $arg) use ($twig, $menu, $chemin, $cat) {
    $n         = $arg['n'];
    $categorie = new controller\getCategorie();
    $categorie->displayCategorie($twig, $menu, $chemin, $cat->getCategories(), $n);
});
