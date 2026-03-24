<?php

declare(strict_types=1);

namespace service;

use model\Annonce;
use model\Photo;

class SuppressionAnnonceService
{
    public function supprimer(int|string $idAnnonce, string $motDePasse): array
    {
        $annonce = Annonce::find($idAnnonce);

        if (!isset($annonce)) {
            return [
                'annonce' => null,
                'motDePasseValide' => false,
            ];
        }

        $motDePasseValide = password_verify($motDePasse, $annonce->mdp);

        if ($motDePasseValide) {
            Photo::where('id_annonce', '=', $idAnnonce)->delete();
            $annonce->delete();
        }

        return [
            'annonce' => $annonce,
            'motDePasseValide' => $motDePasseValide,
        ];
    }
}
