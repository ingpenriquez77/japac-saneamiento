<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inicio_procedimientos', function (Blueprint $table) {
            $table->id();

            // 🔗 Enlaces relacionales con la visita formal previa y el usuario de control
            $table->foreignId('visita_inspeccion_id')->constrained('visita_inspeccions')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');

            // 📄 Nomenclatura del Oficio de Inicio (Ej: "No.: D.J. 015/17")
            $table->string('num_oficio_inicio', 50)->unique();
            $table->date('fecha_notificacion');

            // ⚖️ Bloque Legal y Pruebas
            $table->string('fundamento_legal', 255)->default('Punto 8.2 del Régimen Tarifario Vigente de JAPAC');
            $table->text('hechos_motivo'); // Justificación de la infracción
            $table->string('plazo_concedido', 50)->default('5 días hábiles');

            $table->string('status', 30)->default('En Proceso'); // En Proceso, Solventado, Sancionado
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inicio_procedimientos');
    }
};
