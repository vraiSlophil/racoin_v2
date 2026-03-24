<?php

use controller\CleApiController;
use model\Annonce;
use model\Annonceur;
use model\Categorie;
use model\Departement;

$app->get('/api(/)', function () use ($twig, $chemin) {
    $template = $twig->load('api-documentation.html.twig');
    $menu     = [
        [
            'href' => $chemin,
            'text' => 'Acceuil',
        ],
        [
            'href' => $chemin . '/api',
            'text' => 'Api',
        ],
    ];

    echo $template->render(['breadcrumb' => $menu, 'chemin' => $chemin]);
});

$app->group('/api', function () use ($app, $twig, $menu, $chemin, $cat) {
    $app->group('/annonce', function () use ($app) {
        $app->get('/{id}', function ($request, $response, $arg) {
            $id          = $arg['id'];
            $annonceList = ['id_annonce', 'id_categorie as categorie', 'id_annonceur as annonceur', 'id_departement as departement', 'prix', 'date', 'titre', 'description', 'ville'];
            $return      = Annonce::select($annonceList)->find($id);

            if (!isset($return)) {
                return $response->withStatus(404);
            }

            $return->categorie   = Categorie::find($return->categorie);
            $return->annonceur   = Annonceur::select('email', 'nom_annonceur', 'telephone')->find($return->annonceur);
            $return->departement = Departement::select('id_departement', 'nom_departement')->find($return->departement);
            $links               = [];
            $links['self']['href'] = '/api/annonce/' . $return->id_annonce;
            $return->links       = $links;

            $response->getBody()->write($return->toJson());

            return $response->withHeader('Content-Type', 'application/json');
        });
    });

    $app->group('/annonces(/)', function () use ($app) {
        $app->get('/', function ($request, $response) {
            $annonceList = ['id_annonce', 'prix', 'titre', 'ville'];
            $annonces    = Annonce::all($annonceList);
            $links       = [];

            foreach ($annonces as $annonce) {
                $links['self']['href'] = '/api/annonce/' . $annonce->id_annonce;
                $annonce->links        = $links;
            }

            $links['self']['href'] = '/api/annonces/';
            $annonces->links       = $links;

            $response->getBody()->write($annonces->toJson());

            return $response->withHeader('Content-Type', 'application/json');
        });
    });

    $app->group('/categorie', function () use ($app) {
        $app->get('/{id}', function ($request, $response, $arg) {
            $id       = $arg['id'];
            $annonces = Annonce::select('id_annonce', 'prix', 'titre', 'ville')
                ->where('id_categorie', '=', $id)
                ->get();
            $links = [];

            foreach ($annonces as $annonce) {
                $links['self']['href'] = '/api/annonce/' . $annonce->id_annonce;
                $annonce->links        = $links;
            }

            $categorie              = Categorie::find($id);
            $links['self']['href']  = '/api/categorie/' . $id;
            $categorie->links       = $links;
            $categorie->annonces    = $annonces;

            $response->getBody()->write($categorie->toJson());

            return $response->withHeader('Content-Type', 'application/json');
        });
    });

    $app->group('/categories(/)', function () use ($app) {
        $app->get('/', function ($request, $response) {
            $categories = Categorie::get();
            $links      = [];

            foreach ($categories as $categorie) {
                $links['self']['href'] = '/api/categorie/' . $categorie->id_categorie;
                $categorie->links      = $links;
            }

            $links['self']['href'] = '/api/categories/';
            $categories->links     = $links;

            $response->getBody()->write($categories->toJson());

            return $response->withHeader('Content-Type', 'application/json');
        });
    });

    $app->get('/key', function () use ($twig, $menu, $chemin, $cat) {
        $generateur = new CleApiController();
        $generateur->afficherFormulaireCle($twig, $menu, $chemin, $cat->listerCategories());
    });

    $app->post('/key', function () use ($twig, $menu, $chemin, $cat) {
        $nom        = $_POST['nom'];
        $generateur = new CleApiController();
        $generateur->genererCle($twig, $menu, $chemin, $cat->listerCategories(), $nom);
    });
});
