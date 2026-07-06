<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    // 🔐 Acceso
    'usuario',
    'password',
    'email',

    // 👤 Identidad Base
    'nombre',
    'paterno',
    'materno',
    'sexo',
    'fechanacimiento',
    'lugar_nacimiento',
    'curp',

    // 🛡️ Filtros Relacionales y Seguridad Social
    'departamento_id',
    'puesto_id',
    'telefono',
    'tipo_telefono',
    'nss',
    'estado_operativo',

    // 🏠 Domicilio Completo
    'codigopostal',
    'estado',
    'municipio',
    'colonia',
    'calle',
    'numerocasa'
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // =========================================================================
    // 🛠️ RELACIONES INSTITUCIONALES (MAPEADAS A TU ESQUEMA)
    // =========================================================================

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    public function puesto(): BelongsTo
    {
        return $this->belongsTo(Puesto::class, 'puesto_id');
    }
}
