<?php

declare(strict_types=1);

namespace model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Annonceur extends Model
{
    protected $table = 'annonceur';
    protected $primaryKey = 'id_annonceur';
    public $timestamps = false;

    public function annonce(): HasMany
    {
        return $this->hasMany(Annonce::class, 'id_annonceur');
    }
}
