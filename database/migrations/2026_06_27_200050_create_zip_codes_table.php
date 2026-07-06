<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zip_codes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_postal', 5)->index(); // Índice simple para búsquedas por JS
            $table->string('colonia', 150);
            $table->string('municipio', 100);
            $table->string('estado', 50);
            $table->timestamps();

            // Evitamos duplicar exactamente la misma colonia en el mismo código postal
            $table->unique(['codigo_postal', 'colonia']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zip_codes');
    }
};
