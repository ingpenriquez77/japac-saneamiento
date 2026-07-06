<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nombre', 'codigo'])]
class Departamento extends Model
{
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'departamento_id');
    }
}
