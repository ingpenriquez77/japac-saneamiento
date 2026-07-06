<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CalculoIndIncumplimiento;
use App\Models\Establecimiento;
use App\Models\User;

class CalculoIncumplimientoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Recuperamos las dependencias necesarias creadas en los seeders previos
        $establecimiento = Establecimiento::where('cuenta', '402916-8')->first();
        $userAdmin = User::where('usuario', 'admin')->first();

        if (!$establecimiento || !$userAdmin) {
            $this->command->error("❌ Error: Se requiere ejecutar primero CatalogoSaneamientoSeeder y EstablecimientoYVisitasSeeder.");
            return;
        }

        // 2. Datos base simulando el muestreo real de la hoja de cálculo (Mediterraneo Café / Caffenio de ejemplo)
        $gastoLps = 0.0790;
        $dbo = 232.00; // Límite 200
        $sst = 92.00;  // Límite 200 (No excede)
        $gya = 95.18;  // Límite 75

        $limDbo = 200.00; $limSst = 200.00; $limGya = 75.00;

        // 📐 Fórmulas Matemáticas Institucionales
        $volumenMensual = $gastoLps * 86400 * 30 / 1000; // Recrea los 204.77 m³

        $indDbo = ($dbo > $limDbo) ? (($dbo - $limDbo) / $limDbo) : 0.00; // 0.16
        $indSst = ($sst > $limSst) ? (($sst - $limSst) / $limSst) : 0.00; // 0.00
        $indGya = ($gya > $limGya) ? (($gya - $limGya) / $limGya) : 0.00; // 0.27

        // Determinamos el predominante dinámicamente en el sembrado
        $maxIndice = max($indDbo, $indSst, $indGya); // El más alto es GyA con 0.27
        $contaminante = 'GYA';

        // Carga Contaminante Excedente en Kilogramos
        $cargaKg = (($gya - $limGya) / 1000) * $volumenMensual; // ~4.13 Kg

        // Aplicación del Rango Tarifario de la Tabla III (0.10 a 1.00)
        $cuotaPorKg = 5.808;

        $montoMes = $cargaKg * $cuotaPorKg;
        $montoAnual = $montoMes * 12;

        // 3. Inyección limpia evitando duplicados para la misma fecha de muestreo
        CalculoIndIncumplimiento::firstOrCreate(
            [
                'establecimiento_id' => $establecimiento->id,
                'fecha_muestreo'     => '2026-07-05'
            ],
            [
                'user_id'                    => $userAdmin->id,
                'laboratorio_analisis'       => 'JAPAC',
                'gasto_medio_diario_lps'     => $gastoLps,
                'volumen_mensual_m3'         => $volumenMensual,
                'resultado_dbo'              => $dbo,
                'resultado_sst'              => $sst,
                'resultado_gya'              => $gya,
                'limite_dbo'                 => $limDbo,
                'limite_sst'                 => $limSst,
                'limite_gya'                 => $limGya,
                'indice_dbo'                 => $indDbo,
                'indice_sst'                 => $indSst,
                'indice_gya'                 => $indGya,
                'contaminante_predominante'  => $contaminante,
                'indice_predominante_final'  => $maxIndice,
                'carga_contaminante_kg'      => $cargaKg,
                'cuota_por_kg'               => $cuotaPorKg,
                'monto_pagar_mes'            => $montoMes,
                'monto_pagar_anual'          => $montoAnual,
                'observaciones'              => 'Cálculo de tasación inicial generado automáticamente por el sistema de Saneamiento basado en la Tabla III.'
            ]
        );

        $this->command->info("🎉 ¡Módulo Financiero Inicializado! Tasación por Índice de Incumplimiento registrada.");
    }
}
