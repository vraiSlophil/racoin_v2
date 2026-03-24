<?php

declare(strict_types=1);

namespace service;

use model\ApiKey;

class CleApiService
{
    public function generer(string $nom): array
    {
        if (str_replace(' ', '', $nom) === '') {
            return [
                'succes' => false,
            ];
        }

        $cle = uniqid();
        $apiKey = new ApiKey();

        $apiKey->id_apikey = $cle;
        $apiKey->name_key = trim($nom);
        $apiKey->save();

        return [
            'succes' => true,
            'cle' => $cle,
        ];
    }
}
