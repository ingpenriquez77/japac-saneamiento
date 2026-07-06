<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Departamento;
use App\Models\Puesto;
use App\Models\User;
use App\Models\ZipCode;
use App\Models\Establecimiento;
use App\Models\InspeccionInformal;
use App\Models\VisitaInspeccion; // 👈 IMPORTACIÓN AGREGADA
use Illuminate\Support\Facades\Hash;
use Pdf;

class InitialSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================================================
        // 🏢 1. SEED DE DEPARTAMENTOS / ÁREAS OFICIALES JAPAC
        // =========================================================================
        $depAdmin       = Departamento::create(['nombre' => 'Administración General', 'codigo' => 'ADM-GEN']);
        $depSaneamiento = Departamento::create(['nombre' => 'Saneamiento', 'codigo' => 'SAN']);
        $depSistemas    = Departamento::create(['nombre' => 'Sistemas / TI', 'codigo' => 'TI']);
        $depOperadores  = Departamento::create(['nombre' => 'Operadores de Saneamiento', 'codigo' => 'OP-SAN']);

        $this->command->info("🏢 Departamentos institucionales de JAPAC creados con éxito.");

        // =========================================================================
        // 💼 2. SEED DE PUESTOS / ROLES
        // =========================================================================
        $puestoAdmin    = Puesto::create(['nombre' => 'Administrador Global', 'nivel_acceso' => 'admin', 'departamento_id' => $depAdmin->id]);
        $puestoGerente  = Puesto::create(['nombre' => 'Gerente de Saneamiento', 'nivel_acceso' => 'gerente', 'departamento_id' => $depSaneamiento->id]);
        $puestoDictaminador = Puesto::create(['nombre' => 'Dictaminador de Procedimientos', 'nivel_acceso' => 'gerente', 'departamento_id' => $depSaneamiento->id]);
        $puestoSistemas = Puesto::create(['nombre' => 'Ingeniero de Sistemas / IT', 'nivel_acceso' => 'sistemasIT', 'departamento_id' => $depSistemas->id]);
        $puestoSoporte  = Puesto::create(['nombre' => 'Técnico de Soporte', 'nivel_acceso' => 'sistemasIT', 'departamento_id' => $depSistemas->id]);
        $puestoOperador = Puesto::create(['nombre' => 'Inspector Técnico de Descargas', 'nivel_acceso' => 'operador', 'departamento_id' => $depOperadores->id]);
        $puestoMuestreador = Puesto::create(['nombre' => 'Técnico Muestreador de Laboratorio', 'nivel_acceso' => 'operador', 'departamento_id' => $depOperadores->id]);

        $this->command->info("💼 Catálogo de puestos ramificados configurado correctamente.");

        // =========================================================================
        // 👤 3. SEED DE USUARIOS / PERSONAL BASE
        // =========================================================================
        $this->command->info("⏳ Inyectando cuentas de usuario base corporativas...");

        $userAdmin = User::create([
            'usuario' => 'admin',
            'password' => Hash::make('010704'),
            'nombre' => 'Admin',
            'paterno' => 'JAPAC',
            'materno' => 'CLN',
            'sexo' => 'M',
            'fechanacimiento' => '1990-01-01',
            'lugar_nacimiento' => 'SINALOA',
            'curp' => 'JAPA900101HNECLN01',
            'departamento_id' => $depAdmin->id,
            'puesto_id' => $puestoAdmin->id,
            'email' => 'admin@japac.gob.mx',
            'telefono' => '6671000000',
            'tipo_telefono' => 'CELULAR',
            'nss' => '12345678901',
            'estado_operativo' => 'Activo',
            'codigopostal' => '80000',
            'estado' => 'SINALOA',
            'municipio' => 'CULIACÁN',
            'colonia' => 'CENTRO',
            'calle' => 'AV. ÁLVARO OBREGÓN',
            'numerocasa' => '100'
        ]);

        User::create([
            'usuario' => 'pedro.enriquez',
            'password' => Hash::make('010704'),
            'nombre' => 'Pedro Rafael',
            'paterno' => 'Enriquez',
            'materno' => 'Nevarez',
            'sexo' => 'M',
            'fechanacimiento' => '1993-08-16',
            'lugar_nacimiento' => 'SINALOA',
            'curp' => 'ERNP930816HSLNV00',
            'departamento_id' => $depSistemas->id,
            'puesto_id' => $puestoSistemas->id,
            'email' => 'ing.pedro.enriquez@japac.gob.mx',
            'telefono' => '6679169021',
            'tipo_telefono' => 'CELULAR',
            'nss' => '23169320589',
            'estado_operativo' => 'Activo',
            'codigopostal' => '80050',
            'estado' => 'SINALOA',
            'municipio' => 'CULIACÁN',
            'colonia' => 'INTEGRACIÓN SINALOA',
            'calle' => 'JOSE VASCONCELOS',
            'numerocasa' => '3062'
        ]);

        $this->command->info("👤 Usuarios de prueba 'admin' y 'pedro.enriquez' cargados.");

        // =========================================================================
        // 🗺️ 4. INTEGRACIÓN MASIVA: CÓDIGOS POSTALES DE MÉXICO
        // =========================================================================
        $filePath = storage_path('app/Codigos_Postales.csv');

        if (!file_exists($filePath)) {
            $this->command->error("⚠️ Alerta: No se encontró el archivo CSV en 'storage/app/Codigos_Postales.csv'.");
        } else {
            $file = fopen($filePath, 'r');
            $batch = [];
            $contadorCP = 0;

            $this->command->info("⏳ Procesando catálogo nacional de Códigos Postales...");

            fgetcsv($file, 0, ',', '"');

            while (($datos = fgetcsv($file, 0, ',', '"')) !== false) {
                if (count($datos) >= 4) {
                    $cp        = trim($datos[0]);
                    $colonia   = trim($datos[1]);
                    $municipio = trim($datos[2]);
                    $estado    = trim($datos[3]);

                    if (empty($cp)) continue;

                    $batch[] = [
                        'codigo_postal' => str_pad($cp, 5, "0", STR_PAD_LEFT),
                        'colonia'       => strtoupper($colonia),
                        'municipio'     => strtoupper($municipio),
                        'estado'        => strtoupper($estado),
                        'created_at'    => now(),
                        'updated_at'    => now()
                    ];

                    $contadorCP++;

                    if (count($batch) >= 2000) {
                        ZipCode::insertOrIgnore($batch);
                        $batch = [];
                    }
                }
            }

            if (count($batch) > 0) {
                ZipCode::insertOrIgnore($batch);
            }

            fclose($file);
            $this->command->info("🎉 ¡Éxito total! Se importaron localmente {$contadorCP} asentamientos postales.");
        }

        // =========================================================================
        // 🏢 5. SEED DE ESTABLECIMIENTOS (PADRÓN DE SANEAMIENTO)
        // =========================================================================
        $caffenio = Establecimiento::create([
            'cuenta'                 => '402916-8',
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
            'num_interior'           => null,
            'status'                 => 'Activo',
            'observaciones'          => 'Sucursal tipo Drive-Thru. Cuenta con trampa de grasas en área de preparación de bebidas.'
        ]);

        // =========================================================================
        // 📋 6. SEED DE INSPECCIONES INFORMALES
        // =========================================================================
        $this->command->info("⏳ Creando boleta de infracción de la 'Cocina Económica Yumel'...");

        $inspeccion = InspeccionInformal::create([
            'num_folio'                       => '0228',
            'fecha_infraccion'                => '2025-12-07',
            'hora_infraccion'                 => '11:40:00',
            'nombre_establecimiento_informal' => 'COCINA ECONÓMICA YUMEL',
            'domicilio_informal'              => 'MERCADO GARMENDIA',
            'num_medidor_informal'            => '355970',
            'cuenta_informal'                 => null,
            'señas_particulares'              => 'FACHADA ECONÓMICA',
            'user_id'                         => $userAdmin->id,
            'anomalia_grasas_aceites'        => true,
            'anomalia_sin_permiso'           => false,
            'anomalia_residuos_toxicos'      => false,
            'anomalia_aguas_pluviales'       => false,
            'anomalia_sin_registro_banqueta' => false,
            'observaciones_campo'             => 'Se infracciona porque al revisar el establecimiento se encontró bastante sólido y grasas tapando lodo. El registro se encuentra saturado.',
            'recibio_notificacion'            => 'Se dejó original',
            'status'                          => 'Pendiente'
        ]);

        $inspeccion->inspector = $userAdmin;
        $htmlPdfInf = view('inspecciones_informales.pdf_template', ['inf' => $inspeccion])->render();
        $pdfInfRenderizado = Pdf::loadHTML($htmlPdfInf)->setPaper('letter', 'portrait')->output();

        $inspeccion->archivoPdf()->create([
            'nombre_archivo'   => 'SISTEMA_FOLIO_0228.pdf',
            'tipo_formato'     => 'application/pdf',
            'contenido_base64' => base64_encode($pdfInfRenderizado)
        ]);

        // =========================================================================
        // 📑 7. SEED DE VISITAS DE INSPECCIÓN (MÓDULO FORMAL - NUEVO)
        // =========================================================================
        $this->command->info("⏳ Inyectando Orden de Visita Formal para Caffenio Militar...");

        $visitaFormal = VisitaInspeccion::create([
            'establecimiento_id'     => $caffenio->id,
            'num_visita_inspeccion'  => 'V.I. 001/17',
            'fechavisita_inspeccion' => '2016-11-03 10:30:00',
            'num_oficioVI'           => 'No.: D.J. 008/17',
            'status'                 => 'Notificado',
            'observaciones'          => 'Se ejecuta orden formal de inspección técnica en descargas. Se constata la correcta limpieza y bitácora de la trampa de grasas comercial.'
        ]);

        // Generamos el PDF del template institucional formal y lo guardamos centralizadamente
        $htmlPdfVi = view('visitas_inspeccion.pdf_template', ['visita' => $visitaFormal])->render();
        $pdfViRenderizado = Pdf::loadHTML($htmlPdfVi)->setPaper('letter', 'portrait')->output();

        $visitaFormal->archivos()->create([
            'nombre_archivo'   => 'SISTEMA_VI_No___D_J__008_17.pdf',
            'tipo_formato'     => 'application/pdf',
            'contenido_base64' => base64_encode($pdfViRenderizado)
        ]);

        $this->command->info("🎉 ¡Módulo formal inicializado! Registro de prueba de 'Visitas de Inspección' cargado exitosamente.");
    }
}
