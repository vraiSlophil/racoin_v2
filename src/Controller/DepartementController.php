<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Departement;

class DepartementController
{
    public function listerDepartements(): array
    {
        return Departement::orderBy('nom_departement')->get()->toArray();
    }
}
