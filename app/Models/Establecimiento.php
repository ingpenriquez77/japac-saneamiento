<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'nombre_establecimiento', 'razon_social', 'rfc', 'actividad',
    'calle', 'num_exterior', 'num_interior', 'colonia', 'codigo_postal',
    'telefono', 'num_medidor', 'cuenta', 'correo_electronico',
    'trampas_gra', 'trampas_sst', 'num_permiso', 'fechaemision_permiso',
    'status', 'observaciones', 'empresa_nueva'
])]
class Establecimiento extends Model
{
    protected $casts = [
        'fechaemision_permiso' => 'datetime',
        'empresa_nueva' => 'boolean'
    ];
}
