<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class InicioProcedimiento extends Model
{
    protected $table = 'inicio_procedimientos';
    protected $fillable = ['visita_inspeccion_id', 'user_id', 'num_oficio_inicio', 'fecha_notificacion', 'fundamento_legal', 'hechos_motivo', 'plazo_concedido', 'status'];
    protected $casts = ['fecha_notificacion' => 'date'];

    public function archivos(): MorphMany {
        return $this->morphMany(ArchivoBinario::class, 'documento');
    }

    public function visita(): BelongsTo {
        return $this->belongsTo(VisitaInspeccion::class, 'visita_inspeccion_id');
    }

    public function usuario(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function resolutivo() {
        return $this->hasOne(ResolutivoAdministrativo::class, 'inicio_procedimiento_id');
    }
}
