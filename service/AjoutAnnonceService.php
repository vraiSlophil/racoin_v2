<?php

namespace service;

use DateTimeImmutable;
use DateTimeZone;
use model\Annonce;
use model\Annonceur;

class AjoutAnnonceService
{
    private ValidateurAnnonceService $validateurAnnonceService;

    public function __construct(?ValidateurAnnonceService $validateurAnnonceService = null)
    {
        $this->validateurAnnonceService = $validateurAnnonceService ?? new ValidateurAnnonceService();
    }

    public function ajouter(array $donnees): array
    {
        $erreurs = $this->validateurAnnonceService->validerCreation($donnees);

        if ($erreurs !== []) {
            return [
                'succes' => false,
                'erreurs' => $erreurs,
            ];
        }

        $annonce = new Annonce();
        $annonceur = new Annonceur();

        $annonceur->email = htmlentities((string) ($donnees['email'] ?? ''));
        $annonceur->nom_annonceur = htmlentities((string) ($donnees['nom'] ?? ''));
        $annonceur->telephone = htmlentities((string) ($donnees['phone'] ?? ''));

        $annonce->ville = htmlentities((string) ($donnees['ville'] ?? ''));
        $annonce->id_departement = $donnees['departement'] ?? null;
        $annonce->prix = htmlentities((string) ($donnees['price'] ?? ''));
        $annonce->mdp = password_hash((string) ($donnees['psw'] ?? ''), PASSWORD_DEFAULT);
        $annonce->titre = htmlentities((string) ($donnees['title'] ?? ''));
        $annonce->description = htmlentities((string) ($donnees['description'] ?? ''));
        $annonce->id_categorie = $donnees['categorie'] ?? null;
        $annonce->date = $this->dateCourante();

        $annonceur->save();
        $annonceur->annonce()->save($annonce);

        return [
            'succes' => true,
            'annonce' => $annonce,
            'annonceur' => $annonceur,
        ];
    }

    private function dateCourante(): string
    {
        return (new DateTimeImmutable('now', new DateTimeZone('Europe/Paris')))->format('Y-m-d');
    }
}
