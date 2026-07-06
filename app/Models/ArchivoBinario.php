<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ArchivoBinario extends Model
{
    protected $table = 'archivos_binarios';

    protected $fillable = [
        'documento_type',
        'documento_id',
        'nombre_archivo',
        'tipo_formato',
        'contenido_base64'
    ];

    public function documento(): MorphTo
    {
        return $this->morphTo();
    }
}
