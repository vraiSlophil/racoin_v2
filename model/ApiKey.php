<?php

declare(strict_types=1);

namespace model;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    protected $table = 'apikey';
    protected $primaryKey = 'id_key';
    public $timestamps = false;
}
