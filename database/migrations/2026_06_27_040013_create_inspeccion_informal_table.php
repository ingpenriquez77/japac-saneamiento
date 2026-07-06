<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inspeccion_informal', function (Blueprint $table) {
            $table->id();

            // 📑 Control del Formato Impreso (Boleta Rosa)
            $table->string('num_folio', 20)->unique();
            $table->date('fecha_infraccion');
            $table->time('hora_infraccion');

            // 🏢 Localización de Campo Levantada a Mano
            $table->string('nombre_establecimiento_informal', 150);
            $table->string('domicilio_informal', 255);
            $table->string('num_medidor_informal', 50)->nullable();
            $table->string('cuenta_informal', 50)->nullable();
            $table->string('señas_particulares', 150)->nullable();

            // 👤 Personal Técnico de Saneamiento
            $table->foreignId('user_id')->constrained('users');

            // ☑️ Checklist Rosa de Infracciones (Art. 16, 23, 24, 30, 37, 80 y 91 Ley de Agua Potable)
            $table->boolean('anomalia_sin_permiso')->default(false);
            $table->boolean('anomalia_grasas_aceites')->default(false);
            $table->boolean('anomalia_residuos_toxicos')->default(false);
            $table->boolean('anomalia_aguas_pluviales')->default(false);
            $table->boolean('anomalia_sin_registro_banqueta')->default(false);

            // ✍️ Dictamen y Notificación
            $table->text('observaciones_campo')->nullable();
            $table->string('recibio_notificacion', 100)->nullable();
            $table->string('status', 20)->default('Pendiente');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspeccion_informal');
    }
};
