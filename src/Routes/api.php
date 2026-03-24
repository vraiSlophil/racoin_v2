<?php

declare(strict_types=1);

use App\Controller\CleApiController;
use App\Model\Annonce;
use App\Model\Annonceur;
use App\Model\Categorie;
use App\Model\Departement;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteCollectorProxy;

$app->group('/api', function (RouteCollectorProxy $group) use ($twig, $menu, $chemin, $cat) {
    $group->get('', function (ServerRequestInterface $request, ResponseInterface $response) use ($twig, $chemin): ResponseInterface {
        $template = $twig->load('api-documentation.html.twig');
        $menu = [
            [
                'href' => $chemin,
                'text' => 'Acceuil',
            ],
            [
                'href' => $chemin . 'api',
                'text' => 'Api',
            ],
        ];

        echo $template->render(['breadcrumb' => $menu, 'chemin' => $chemin]);

        return $response;
    });

    $group->group('/annonce', function (RouteCollectorProxy $group) {
        $group->get('/{id}', function (ServerRequestInterface $request, ResponseInterface $response, array $arg): ResponseInterface {
            $id = $arg['id'];
            $annonceList = ['id_annonce', 'id_categorie as categorie', 'id_annonceur as annonceur', 'id_departement as departement', 'prix', 'date', 'titre', 'description', 'ville'];
            $annonce = Annonce::select($annonceList)->find($id);

            if (!isset($annonce)) {
                return $response->withStatus(404);
            }

            $payload = $annonce->toArray();
            $payload['categorie'] = Categorie::find($annonce->categorie)?->toArray();
            $payload['annonceur'] = Annonceur::select('email', 'nom_annonceur', 'telephone')->find($annonce->annonceur)?->toArray();
            $payload['departement'] = Departement::select('id_departement', 'nom_departement')->find($annonce->departement)?->toArray();
            $payload['links'] = [
                'self' => [
                    'href' => '/api/annonce/' . $annonce->id_annonce,
                ],
            ];

            $response->getBody()->write(json_encode($payload, JSON_THROW_ON_ERROR));

            return $response->withHeader('Content-Type', 'application/json');
        });
    });

    $group->group('/annonces', function (RouteCollectorProxy $group) {
        $group->get('', function (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
            $annonceList = ['id_annonce', 'prix', 'titre', 'ville'];
            $annonces    = Annonce::all($annonceList);

            foreach ($annonces as $annonce) {
                $annonce->links = [
                    'self' => [
                        'href' => '/api/annonce/' . $annonce->id_annonce,
                    ],
                ];
            }

            $response->getBody()->write($annonces->toJson());

            return $response->withHeader('Content-Type', 'application/json');
        });
    });

    $group->group('/categorie', function (RouteCollectorProxy $group) {
        $group->get('/{id}', function (ServerRequestInterface $request, ResponseInterface $response, array $arg): ResponseInterface {
            $id = $arg['id'];
            $annonces = Annonce::select('id_annonce', 'prix', 'titre', 'ville')
                ->where('id_categorie', '=', $id)
                ->get();
            $categorie = Categorie::find($id);

            if (!isset($categorie)) {
                return $response->withStatus(404);
            }

            $links = [];

            foreach ($annonces as $annonce) {
                $links['self']['href'] = '/api/annonce/' . $annonce->id_annonce;
                $annonce->links        = $links;
            }

            $links['self']['href']  = '/api/categorie/' . $id;
            $categorie->links       = $links;
            $categorie->annonces    = $annonces;

            $response->getBody()->write($categorie->toJson());

            return $response->withHeader('Content-Type', 'application/json');
        });
    });

    $group->group('/categories', function (RouteCollectorProxy $group) {
        $group->get('', function (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
            $categories = Categorie::get();

            foreach ($categories as $categorie) {
                $categorie->links = [
                    'self' => [
                        'href' => '/api/categorie/' . $categorie->id_categorie,
                    ],
                ];
            }

            $response->getBody()->write($categories->toJson());

            return $response->withHeader('Content-Type', 'application/json');
        });
    });

    $group->get('/key', function (ServerRequestInterface $request, ResponseInterface $response) use ($twig, $menu, $chemin, $cat): ResponseInterface {
        $generateur = new CleApiController();
        $generateur->afficherFormulaireCle($twig, $menu, $chemin, $cat->listerCategories());
        return $response;
    });

    $group->post('/key', function (ServerRequestInterface $request, ResponseInterface $response) use ($twig, $menu, $chemin, $cat): ResponseInterface {
        $donnees = $request->getParsedBody();
        $donnees = is_array($donnees) ? $donnees : [];
        $nom = (string) ($donnees['nom'] ?? '');
        $generateur = new CleApiController();
        $generateur->genererCle($twig, $menu, $chemin, $cat->listerCategories(), $nom);
        return $response;
    });
});
