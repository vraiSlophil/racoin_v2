<?php

declare(strict_types=1);

namespace controller;

use model\Departement;

class DepartementController
{
    public function listerDepartements(): array
    {
        return Departement::orderBy('nom_departement')->get()->toArray();
    }
}
