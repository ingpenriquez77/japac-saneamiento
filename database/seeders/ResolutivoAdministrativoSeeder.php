<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ResolutivoAdministrativo;
use App\Models\InicioProcedimiento;
use App\Models\User;
use Pdf;

class ResolutivoAdministrativoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Recuperamos las dependencias previas
        $procedimiento = InicioProcedimiento::where('num_oficio_inicio', 'No.: D.J. 015/17')->first();
        $userAdmin = User::where('usuario', 'admin')->first();

        if (!$procedimiento || !$userAdmin) {
            $this->command->error("❌ Error: Se requiere ejecutar primero InicioProcedimientoSeeder.");
            return;
        }

        // 2. Inyección de la Sentencia en la Base de Datos
        $res = ResolutivoAdministrativo::firstOrCreate(
            ['num_resolutivo' => 'No.: D.J. RES-042/17'],
            [
                'inicio_procedimiento_id' => $procedimiento->id,
                'user_id'                 => $userAdmin->id,
                'fecha_resolucion'        => '2026-07-05',
                'monto_sancion_pesos'     => 24850.50,
                'sancion_adicional'       => 'Clausura temporal del punto de descarga y adecuación obligatoria de la trampa de grasa en un periodo no mayor a 15 días.',
                'considerandos_legales'   => 'Resultando que el establecimiento comercial fue notificado en tiempo y forma bajo el oficio No.: D.J. 015/17 y habiendo fenecido el plazo legal de 5 días sin que presentara pruebas de descargo ni bitácoras de mantenimiento, se le declara en rebeldía. Se resuelve imponer la sanción económica líquida por violación reiterada a los límites de la Tabla I (Grasas y Aceites).',
                'status_final'            => 'Notificado'
            ]
        );

        // Actualizamos de forma automática el estatus del expediente padre
        $procedimiento->update(['status' => 'Sancionado']);

        // 3. Generación e inserción del Binario PDF en caso de no existir
        if ($res->archivos()->count() === 0) {
            $res->load('inicioProcedimiento.visita.establecimiento');

            $htmlPdf = view('resolutivo_administrativos.pdf_template', ['res' => $res])->render();
            $pdfRenderizado = Pdf::loadHTML($htmlPdf)->setPaper('letter', 'portrait')->output();

            $res->archivos()->create([
                'nombre_archivo'   => 'SISTEMA_RES_No___D_J__RES_042_17.pdf',
                'tipo_formato'     => 'application/pdf',
                'contenido_base64' => base64_encode($pdfRenderizado)
            ]);
        }

        $this->command->info("🎉 ¡Ciclo Legal Concluido! Sentencia 'No.: D.J. RES-042/17' inyectada en archivos_binarios.");
    }
}
