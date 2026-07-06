<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class VisitaInspeccion extends Model
{
    // 🏢 Nombre de la tabla tal cual lo definiste en tu migración
    protected $table = 'visita_inspeccions';

    // 📝 Atributos asignables de forma masiva (Fillable)
    protected $fillable = [
        'establecimiento_id',
        'num_visita_inspeccion',
        'fechavisita_inspeccion',
        'num_oficioVI',
        'status',
        'observaciones'
    ];

    // 📅 Conversión de tipos nativos (Casts)
    protected $casts = [
        'fechavisita_inspeccion' => 'datetime',
    ];

    /**
     * 🔮 Relación Polimórfica Universal (Muchos a Muchos / MorphMany)
     * Conecta este modelo con la tabla centralizada de 'archivos_binarios'
     */
    public function archivos(): MorphMany
    {
        return $this->morphMany(ArchivoBinario::class, 'documento');
    }

    /**
     * 🏢 Relación Inversa: Una visita pertenece a un único establecimiento regulado
     */
    public function establecimiento(): BelongsTo
    {
        return $this->belongsTo(Establecimiento::class, 'establecimiento_id');
    }

    public function inicioProcedimiento() {
        return $this->hasOne(InicioProcedimiento::class, 'visita_inspeccion_id');
    }
}
