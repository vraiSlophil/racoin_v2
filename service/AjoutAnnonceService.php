<?php

declare(strict_types=1);

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

        $annonceur->email = $this->champTexte($donnees, 'email');
        $annonceur->nom_annonceur = $this->champTexte($donnees, 'nom');
        $annonceur->telephone = $this->champTexte($donnees, 'phone');

        $annonce->ville = $this->champTexte($donnees, 'ville');
        $annonce->id_departement = $donnees['departement'] ?? null;
        $annonce->prix = $this->champTexte($donnees, 'price');
        $annonce->mdp = password_hash((string) ($donnees['psw'] ?? ''), PASSWORD_DEFAULT);
        $annonce->titre = $this->champTexte($donnees, 'title');
        $annonce->description = $this->champTexte($donnees, 'description');
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

    private function champTexte(array $donnees, string $cle): string
    {
        return trim((string) ($donnees[$cle] ?? ''));
    }
}
