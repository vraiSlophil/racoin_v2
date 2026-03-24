<?php

namespace controller;

use model\Departement;

class DepartementController {

    protected $departments = array();

    public function listerDepartements() {
        return Departement::orderBy('nom_departement')->get()->toArray();
    }
}
