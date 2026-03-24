<?php

declare(strict_types=1);

namespace service;

use model\Annonce;
use model\Annonceur;
use model\Photo;

class PresentateurAnnonceService
{
    public function presenterListe(iterable $annonces, string $urlPhotoParDefaut, bool $inclureNomAnnonceur = true): array
    {
        $annoncesPresentees = [];

        foreach ($annonces as $annonce) {
            $annoncesPresentees[] = $this->presenterAnnonce($annonce, $urlPhotoParDefaut, $inclureNomAnnonceur);
        }

        return $annoncesPresentees;
    }

    public function presenterAnnonce(Annonce $annonce, string $urlPhotoParDefaut, bool $inclureNomAnnonceur = true): Annonce
    {
        $annonce->nb_photo = Photo::where('id_annonce', '=', $annonce->id_annonce)->count();

        if ($annonce->nb_photo > 0) {
            $annonce->url_photo = Photo::select('url_photo')
                ->where('id_annonce', '=', $annonce->id_annonce)
                ->first()
                ?->url_photo ?? $urlPhotoParDefaut;
        } else {
            $annonce->url_photo = $urlPhotoParDefaut;
        }

        if ($inclureNomAnnonceur) {
            $annonce->nom_annonceur = Annonceur::select('nom_annonceur')
                ->where('id_annonceur', '=', $annonce->id_annonceur)
                ->first()
                ?->nom_annonceur ?? '';
        }

        return $annonce;
    }
}
