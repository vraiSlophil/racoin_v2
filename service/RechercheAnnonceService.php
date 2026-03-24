<?php

declare(strict_types=1);

namespace service;

use model\Annonce;
use model\Categorie;

class RechercheAnnonceService
{
    public function rechercher(array $filtres): iterable
    {
        if ($this->rechercheSansFiltre($filtres)) {
            return Annonce::all();
        }

        $query = Annonce::select();

        $motClef = (string) ($filtres['motclef'] ?? '');
        $codePostal = (string) ($filtres['codepostal'] ?? '');
        $categorie = (string) ($filtres['categorie'] ?? '');
        $prixMin = (string) ($filtres['prix-min'] ?? 'Min');
        $prixMax = (string) ($filtres['prix-max'] ?? 'Max');

        if (str_replace(' ', '', $motClef) !== '') {
            $query->where('description', 'like', '%' . $motClef . '%');
        }

        if (str_replace(' ', '', $codePostal) !== '') {
            $query->where('ville', '=', $codePostal);
        }

        if ($categorie !== 'Toutes catégories' && $categorie !== '-----') {
            $idCategorie = Categorie::select('id_categorie')
                ->where('id_categorie', '=', $categorie)
                ->first()
                ->id_categorie;

            $query->where('id_categorie', '=', $idCategorie);
        }

        $this->ajouterFiltrePrix($query, $prixMin, $prixMax);

        return $query->get();
    }

    private function rechercheSansFiltre(array $filtres): bool
    {
        $motClef = str_replace(' ', '', (string) ($filtres['motclef'] ?? ''));
        $codePostal = str_replace(' ', '', (string) ($filtres['codepostal'] ?? ''));
        $categorie = (string) ($filtres['categorie'] ?? '');
        $prixMin = (string) ($filtres['prix-min'] ?? 'Min');
        $prixMax = (string) ($filtres['prix-max'] ?? 'Max');

        return $motClef === ''
            && $codePostal === ''
            && ($categorie === 'Toutes catégories' || $categorie === '-----')
            && $prixMin === 'Min'
            && ($prixMax === 'Max' || $prixMax === 'nolimit');
    }

    private function ajouterFiltrePrix($query, string $prixMin, string $prixMax): void
    {
        if ($prixMin !== 'Min' && $prixMax !== 'Max') {
            if ($prixMax !== 'nolimit') {
                $query->whereBetween('prix', [$prixMin, $prixMax]);
                return;
            }

            $query->where('prix', '>=', $prixMin);
            return;
        }

        if ($prixMax !== 'Max' && $prixMax !== 'nolimit') {
            $query->where('prix', '<=', $prixMax);
            return;
        }

        if ($prixMin !== 'Min') {
            $query->where('prix', '>=', $prixMin);
        }
    }
}
