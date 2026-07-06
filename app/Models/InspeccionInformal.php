<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class InspeccionInformal extends Model
{
    protected $table = 'inspeccion_informal';

    protected $fillable = [
        'num_folio',
        'fecha_infraccion',
        'hora_infraccion',
        'nombre_establecimiento_informal',
        'domicilio_informal',
        'num_medidor_informal',
        'cuenta_informal',
        'señas_particulares',
        'user_id',
        'anomalia_sin_permiso',
        'anomalia_grasas_aceites',
        'anomalia_residuos_toxicos',
        'anomalia_aguas_pluviales',
        'anomalia_sin_registro_banqueta',
        'observaciones_campo',
        'recibio_notificacion',
        'status'
    ];

    protected $casts = [
        'anomalia_sin_permiso'           => 'boolean',
        'anomalia_grasas_aceites'        => 'boolean',
        'anomalia_residuos_toxicos'      => 'boolean',
        'anomalia_aguas_pluviales'       => 'boolean',
        'anomalia_sin_registro_banqueta' => 'boolean',
        'fecha_infraccion'               => 'date'
    ];

    /**
     * 🔮 Relación Polimórfica Universal (Debe ser un String puro, sin Closures internos)
     */
    public function archivoPdf(): MorphMany
    {
        // El segundo argumento DEBE ser la palabra 'documento' como string plano
        return $this->morphMany(ArchivoBinario::class, 'documento');
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
