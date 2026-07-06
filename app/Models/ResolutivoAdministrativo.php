<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ResolutivoAdministrativo extends Model
{
    protected $table = 'resolutivo_administrativos';

    protected $fillable = [
        'inicio_procedimiento_id',
        'user_id',
        'num_resolutivo',
        'fecha_resolucion',
        'monto_sancion_pesos',
        'sancion_adicional',
        'considerandos_legales',
        'status_final'
    ];

    protected $casts = [
        'fecha_resolucion' => 'date',
        'monto_sancion_pesos' => 'decimal:2'
    ];

    /**
     * 🔗 Enlace Polimórfico con la tabla archivos_binarios
     */
    public function archivos(): MorphMany
    {
        // Usa 'documento' si tus columnas son documento_type/documento_id
        // O cambia a 'archivo' si tus columnas son archivo_type/archivo_id
        return $this->morphMany(ArchivoBinario::class, 'documento');
    }

    public function inicioProcedimiento(): BelongsTo
    {
        return $this->belongsTo(InicioProcedimiento::class, 'inicio_procedimiento_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
