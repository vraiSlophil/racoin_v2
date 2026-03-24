<?php

declare(strict_types=1);

namespace model;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'region';
    protected $primaryKey = 'id_region';
    public $timestamps = false;
}
