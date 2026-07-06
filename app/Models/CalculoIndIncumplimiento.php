<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CalculoIndIncumplimiento extends Model
{
    protected $table = 'calculo_ind_incumplimientos';

    protected $guarded = ['id'];

    protected $casts = [
        'fecha_muestreo' => 'date',
        'volumen_mensual_m3' => 'float',
        'indice_predominante_final' => 'float',
        'monto_pagar_mes' => 'float'
    ];

    /**
     * 🔮 Relación Polimórfica Universal: Vincula el PDF de la hoja de cálculo
     */
    public function archivos(): MorphMany
    {
        return $this->morphMany(ArchivoBinario::class, 'documento');
    }

    public function establecimiento(): BelongsTo
    {
        return $this->belongsTo(Establecimiento::class, 'establecimiento_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
