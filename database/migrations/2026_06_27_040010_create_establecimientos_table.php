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
        Schema::create('establecimientos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_establecimiento', 100);
            $table->string('razon_social', 100);
            $table->string('rfc', 15);
            $table->string('actividad', 50);
            $table->string('calle', 100);
            $table->integer('num_exterior');
            $table->string('num_interior', 10)->nullable();
            $table->string('colonia', 100);
            $table->integer('codigo_postal');
            $table->string('telefono', 20);
            $table->string('num_medidor', 50);
            $table->string('cuenta', 50);
            $table->string('correo_electronico', 100);
            $table->integer('trampas_gra')->nullable();
            $table->integer('trampas_sst')->nullable();
            $table->string('num_permiso', 30)->nullable();
            $table->dateTime('fechaemision_permiso')->nullable();
            $table->string('status', 20)->default('Activo');
            $table->text('observaciones')->nullable();
            $table->boolean('empresa_nueva')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('establecimientos');
    }
};
