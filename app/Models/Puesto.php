<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Puesto extends Model
{
    protected $fillable = ['nombre', 'nivel_acceso', 'departamento_id'];

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'puesto_id');
    }
}
