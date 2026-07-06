<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calculo_ind_incumplimientos', function (Blueprint $table) {
            $table->id();

            // 🔗 Vinculación con el Padrón de JAPAC y el usuario que calcula
            $table->foreignId('establecimiento_id')->constrained('establecimientos')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');

            // 📅 Periodo de Facturación / Muestreo
            $table->date('fecha_muestreo');
            $table->string('laboratorio_analisis', 100)->default('JAPAC'); // JAPAC o Lab Externo

            // 💧 Datos Hidráulicos Reales de Campo
            $table->decimal('gasto_medio_diario_lps', 8, 4); // Litros por segundo (Ej: 0.0790)
            $table->decimal('volumen_mensual_m3', 10, 2);    // m³ al mes (Gasto * 86400 * 30 / 1000) (Ej: 204.77)

            // 🧪 Resultados del Análisis Físico-Químico (Mg/Lt)
            $table->decimal('resultado_dbo', 8, 2);
            $table->decimal('resultado_sst', 8, 2);
            $table->decimal('resultado_gya', 8, 2);

            // 🎯 Límites Máximos Permisibles Aplicados (Tabla I - Promedio Mensual)
            $table->decimal('limite_dbo', 8, 2)->default(200.00);
            $table->decimal('limite_sst', 8, 2)->default(200.00);
            $table->decimal('limite_gya', 8, 2)->default(75.00);

            // 📉 Índices de Incumplimiento Calculados Individuales
            // Fórmula: (Resultado - Límite) / Límite
            $table->decimal('indice_dbo', 6, 2)->default(0.00);
            $table->decimal('indice_sst', 6, 2)->default(0.00);
            $table->decimal('indice_gya', 6, 2)->default(0.00);

            // 👑 El Contaminante Mayor (El que dictamina el cobro final)
            $table->string('contaminante_predominante', 20); // DBO, SST o GYA
            $table->decimal('indice_predominante_final', 6, 2); // El valor del índice mayor (Ej: 0.54)

            // 💰 Carga Contaminante e Importes Finales (Tabla III)
            $table->decimal('carga_contaminante_kg', 10, 2);   // Kilogramos de excedente (Ej: 16.79)
            $table->decimal('cuota_por_kg', 8, 3);             // Pesos por Kg según rango de Tabla III (Ej: 5.808)
            $table->decimal('monto_pagar_mes', 12, 2);         // Importe mensual (Kg * Cuota) (Ej: 97.48)
            $table->decimal('monto_pagar_anual', 12, 2);       // Importe 12 meses (Mes * 12) (Ej: 1,169.76)

            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calculo_ind_incumplimientos');
    }
};
