<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Establecimiento;
use App\Models\InspeccionInformal;
use App\Models\VisitaInspeccion;
use App\Models\User;
use Pdf;

class EstablecimientoYVisitasSeeder extends Seeder
{
    public function run(): void
    {
        $userAdmin = User::where('usuario', 'admin')->first();

        // 🏢 Padrón Base: Caffenio Militar
        $caffenio = Establecimiento::firstOrCreate(
            ['cuenta' => '402916-8'],
            [
                'num_medidor'            => 'CFN-99431-M',
                'empresa_nueva'          => false,
                'nombre_establecimiento' => 'CAFFENIO MILITAR',
                'razon_social'           => 'CAFFE S.A. DE C.V.',
                'rfc'                    => 'CAF020315LN4',
                'actividad'              => 'RESTAURANTE / VENTA DE CAFÉ',
                'trampas_gra'            => 1,
                'trampas_sst'            => 0,
                'num_permiso'            => 'JAPAC-SAN-2026-084',
                'fechaemision_permiso'   => '2026-01-15',
                'telefono'               => '6677124050',
                'correo_electronico'     => 'sucursal.militar@caffenio.com',
                'codigo_postal'          => 80200,
                'colonia'                => 'CUAUHTÉMOC',
                'calle'                  => 'CALZADA HEROICO COLEGIO MILITAR',
                'num_exterior'           => 1240,
                'status'                 => 'Activo',
                'observaciones'          => 'Sucursal tipo Drive-Thru. Cuenta con trampa de grasas.'
            ]
        );

        // 📝 Registro de Inspección Informal
        $inspeccion = InspeccionInformal::firstOrCreate(
            ['num_folio' => '0228'],
            [
                'fecha_infraccion'                => '2025-12-07',
                'hora_infraccion'                 => '11:40:00',
                'nombre_establecimiento_informal' => 'COCINA ECONÓMICA YUMEL',
                'domicilio_informal'              => 'MERCADO GARMENDIA',
                'num_medidor_informal'            => '355970',
                'señas_particulares'              => 'FACHADA ECONÓMICA',
                'user_id'                         => $userAdmin->id,
                'anomalia_grasas_aceites'        => true,
                'observaciones_campo'             => 'Se encontró bastante sólido y grasas tapando lodo. Registro saturado.',
                'recibio_notificacion'            => 'Se dejó original',
                'status'                          => 'Pendiente'
            ]
        );

        if ($inspeccion->archivoPdf()->count() === 0) {
            $inspeccion->inspector = $userAdmin;
            $htmlPdfInf = view('inspecciones_informales.pdf_template', ['inf' => $inspeccion])->render();
            $pdfInf = Pdf::loadHTML($htmlPdfInf)->setPaper('letter', 'portrait')->output();
            $inspeccion->archivoPdf()->create([
                'nombre_archivo'   => 'SISTEMA_FOLIO_0228.pdf',
                'tipo_formato'     => 'application/pdf',
                'contenido_base64' => base64_encode($pdfInf)
            ]);
        }

        // 📑 Registro de Visita de Inspección Formal
        $visitaFormal = VisitaInspeccion::firstOrCreate(
            ['num_oficioVI' => 'No.: D.J. 008/17'],
            [
                'establecimiento_id'     => $caffenio->id,
                'num_visita_inspeccion'  => 'V.I. 001/17',
                'fechavisita_inspeccion' => '2016-11-03 10:30:00',
                'status'                 => 'Notificado',
                'observaciones'          => 'Se ejecuta orden formal de inspección técnica en descargas. Se constata correcta limpieza de trampa de grasas.'
            ]
        );

        if ($visitaFormal->archivos()->count() === 0) {
            $htmlPdfVi = view('visitas_inspeccion.pdf_template', ['visita' => $visitaFormal])->render();
            $pdfVi = Pdf::loadHTML($htmlPdfVi)->setPaper('letter', 'portrait')->output();
            $visitaFormal->archivos()->create([
                'nombre_archivo'   => 'SISTEMA_VI_No___D_J__008_17.pdf',
                'tipo_formato'     => 'application/pdf',
                'contenido_base64' => base64_encode($pdfVi)
            ]);
        }
    }
}
