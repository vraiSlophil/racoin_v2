<?php

use controller\CleApiController;
use model\Annonce;
use model\Annonceur;
use model\Categorie;
use model\Departement;
use Slim\Routing\RouteCollectorProxy;

$app->group('/api', function (RouteCollectorProxy $group) use ($twig, $menu, $chemin, $cat) {
    $group->get('', function ($request, $response) use ($twig, $chemin) {
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

        return $response;
    });

    $group->group('/annonce', function (RouteCollectorProxy $group) {
        $group->get('/{id}', function ($request, $response, $arg) {
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

    $group->group('/annonces', function (RouteCollectorProxy $group) {
        $group->get('', function ($request, $response) {
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

    $group->group('/categorie', function (RouteCollectorProxy $group) {
        $group->get('/{id}', function ($request, $response, $arg) {
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

    $group->group('/categories', function (RouteCollectorProxy $group) {
        $group->get('', function ($request, $response) {
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

    $group->get('/key', function ($request, $response) use ($twig, $menu, $chemin, $cat) {
        $generateur = new CleApiController();
        $generateur->afficherFormulaireCle($twig, $menu, $chemin, $cat->listerCategories());
        return $response;
    });

    $group->post('/key', function ($request, $response) use ($twig, $menu, $chemin, $cat) {
        $donnees    = $request->getParsedBody();
        $nom        = (string) ($donnees['nom'] ?? '');
        $generateur = new CleApiController();
        $generateur->genererCle($twig, $menu, $chemin, $cat->listerCategories(), $nom);
        return $response;
    });
});
