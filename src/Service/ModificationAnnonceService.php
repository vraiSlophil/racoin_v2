<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Annonce;
use App\Model\Annonceur;
use App\Model\Categorie;
use App\Model\Departement;
use DateTimeImmutable;
use DateTimeZone;

class ModificationAnnonceService
{
    private ValidateurAnnonceService $validateurAnnonceService;

    public function __construct(?ValidateurAnnonceService $validateurAnnonceService = null)
    {
        $this->validateurAnnonceService = $validateurAnnonceService ?? new ValidateurAnnonceService();
    }

    public function chargerFormulaire(int|string $idAnnonce, string $motDePasse): array
    {
        $annonce = Annonce::find($idAnnonce);

        if (!isset($annonce)) {
            return [
                'annonce' => null,
                'annonceur' => null,
                'motDePasseValide' => false,
                'categorieCourante' => null,
                'departementCourant' => null,
            ];
        }

        return [
            'annonce' => $annonce,
            'annonceur' => Annonceur::find($annonce->id_annonceur),
            'motDePasseValide' => password_verify($motDePasse, $annonce->mdp),
            'categorieCourante' => Categorie::find($annonce->id_categorie)?->nom_categorie,
            'departementCourant' => Departement::find($annonce->id_departement)?->nom_departement,
        ];
    }

    public function modifier(int|string $idAnnonce, array $donnees): array
    {
        $annonce = Annonce::find($idAnnonce);

        if (!isset($annonce)) {
            return [
                'succes' => false,
                'introuvable' => true,
                'erreurs' => [],
            ];
        }

        $erreurs = $this->validateurAnnonceService->validerModification($donnees);

        if ($erreurs !== []) {
            return [
                'succes' => false,
                'introuvable' => false,
                'erreurs' => $erreurs,
            ];
        }

        $annonceur = Annonceur::find($annonce->id_annonceur);

        $annonceur->email = $this->champTexte($donnees, 'email');
        $annonceur->nom_annonceur = $this->champTexte($donnees, 'nom');
        $annonceur->telephone = $this->champTexte($donnees, 'phone');

        $annonce->ville = $this->champTexte($donnees, 'ville');
        $annonce->id_departement = $donnees['departement'] ?? null;
        $annonce->prix = $this->champTexte($donnees, 'price');
        $annonce->titre = $this->champTexte($donnees, 'title');
        $annonce->description = $this->champTexte($donnees, 'description');
        $annonce->id_categorie = $donnees['categorie'] ?? null;
        $annonce->date = $this->dateCourante();

        $nouveauMotDePasse = trim((string) ($donnees['psw'] ?? ''));
        if ($nouveauMotDePasse !== '') {
            $annonce->mdp = password_hash($nouveauMotDePasse, PASSWORD_DEFAULT);
        }

        $annonceur->save();
        $annonce->save();

        return [
            'succes' => true,
            'introuvable' => false,
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
