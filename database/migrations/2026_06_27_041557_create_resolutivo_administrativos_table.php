<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resolutivo_administrativos', function (Blueprint $table) {
            $table->id();

            // 🔗 Relaciones base
            $table->foreignId('inicio_procedimiento_id')->constrained('inicio_procedimientos')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');

            // 📄 Datos oficiales del resolutivo
            $table->string('num_resolutivo', 50)->unique(); // Ej: "No.: D.J. RES-042/17"
            $table->date('fecha_resolucion');

            // 💰 Multas y Sanciones
            $table->decimal('monto_sancion_pesos', 12, 2)->default(0.00);
            $table->string('sancion_adicional', 255)->nullable(); // Ej: "Clausura temporal de descarga", "Adecuación forzosa de trampa"

            // ✍️ Dictamen final
            $table->text('considerandos_legales'); // Resumen de hechos y justificación jurídica final
            $table->string('status_final', 30)->default('Notificado'); // Notificado, Pagado, En Impugnación

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resolutivo_administrativos');
    }
};
