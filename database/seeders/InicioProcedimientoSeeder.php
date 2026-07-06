<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InicioProcedimiento;
use App\Models\VisitaInspeccion;
use App\Models\User;
use Pdf;

class InicioProcedimientoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Recuperamos las dependencias existentes
        $visita = VisitaInspeccion::where('num_oficioVI', 'No.: D.J. 008/17')->first();
        $userAdmin = User::where('usuario', 'admin')->first();

        if (!$visita || !$userAdmin) {
            $this->command->error("❌ Error: Se requiere ejecutar CatalogoSaneamientoSeeder y EstablecimientoYVisitasSeeder primero.");
            return;
        }

        // 2. Inyección del Registro Jurídico con firstOrCreate para evitar duplicidades
        $proc = InicioProcedimiento::firstOrCreate(
            ['num_oficio_inicio' => 'No.: D.J. 015/17'],
            [
                'visita_inspeccion_id' => $visita->id,
                'user_id'              => $userAdmin->id,
                'fecha_notificacion'   => '2026-07-05',
                'fundamento_legal'     => 'Punto 8.2 del Régimen Tarifario Vigente de JAPAC y Ley de Agua Potable del Estado de Sinaloa',
                'hechos_motivo'        => 'Se radica el inicio del procedimiento administrativo formal debido a que se constató en los resultados de laboratorio del muestreo periódico concentraciones que sobrepasan significativamente los Límites Máximos Permisibles (Tabla I), específicamente en el parámetro de Grasas y Aceites (GyA), registrando un índice de incumplimiento de 0.27.',
                'plazo_concedido'      => '5 días hábiles',
                'status'               => 'En Proceso'
            ]
        );

        // 3. Generación Polimórfica del PDF del Oficio en caso de no existir en la tabla binaria
        if ($proc->archivos()->count() === 0) {
            $htmlPdf = view('inicio_procedimientos.pdf_template', ['proc' => $proc])->render();
            $pdfRenderizado = Pdf::loadHTML($htmlPdf)->setPaper('letter', 'portrait')->output();

            $proc->archivos()->create([
                'nombre_archivo'   => 'SISTEMA_PROC_No___D_J__015_17.pdf',
                'tipo_formato'     => 'application/pdf',
                'contenido_base64' => base64_encode($pdfRenderizado)
            ]);
        }

        $this->command->info("🎉 ¡Módulo Contencioso Inicializado! Oficio legal 'No.: D.J. 015/17' sembrado con éxito.");
    }
}
