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
        Schema::create('archivos_binarios', function (Blueprint $table) {
            $table->id();

            // 🔮 Laravel ya crea los campos y el índice compuesto aquí de forma nativa:
            $table->morphs('documento');

            // 📑 Atributos de Control del Archivo
            $table->string('nombre_archivo', 150);
            $table->string('tipo_formato', 50)->default('application/pdf');

            // 💾 Contenedor Binario en Texto (Base64)
            $table->longText('contenido_base64');

            $table->timestamps();

            // 🚫 BORRAMOS LA LÍNEA DEL $table->index QUE ESTABA AQUÍ REPETIDA
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archivos_binarios');
    }
};
