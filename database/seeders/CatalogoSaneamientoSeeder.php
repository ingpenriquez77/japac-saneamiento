<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Departamento;
use App\Models\Puesto;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CatalogoSaneamientoSeeder extends Seeder
{
    public function run(): void
    {
        // 🏢 Departamentos Oficiales
        $depAdmin       = Departamento::firstOrCreate(['nombre' => 'Administración General'], ['codigo' => 'ADM-GEN']);
        $depSaneamiento = Departamento::firstOrCreate(['nombre' => 'Saneamiento'], ['codigo' => 'SAN']);
        $depSistemas    = Departamento::firstOrCreate(['nombre' => 'Sistemas / TI'], ['codigo' => 'TI']);
        $depOperadores  = Departamento::firstOrCreate(['nombre' => 'Operadores de Saneamiento'], ['codigo' => 'OP-SAN']);

        // 💼 Catálogo de Puestos
        $puestoAdmin    = Puesto::firstOrCreate(['nombre' => 'Administrador Global'], ['nivel_acceso' => 'admin', 'departamento_id' => $depAdmin->id]);
        $puestoGerente  = Puesto::firstOrCreate(['nombre' => 'Gerente de Saneamiento'], ['nivel_acceso' => 'gerente', 'departamento_id' => $depSaneamiento->id]);
        $puestoDictaminador = Puesto::firstOrCreate(['nombre' => 'Dictaminador de Procedimientos'], ['nivel_acceso' => 'gerente', 'departamento_id' => $depSaneamiento->id]);
        $puestoSistemas = Puesto::firstOrCreate(['nombre' => 'Ingeniero de Sistemas / IT'], ['nivel_acceso' => 'sistemasIT', 'departamento_id' => $depSistemas->id]);
        $puestoSoporte  = Puesto::firstOrCreate(['nombre' => 'Técnico de Soporte'], ['nivel_acceso' => 'sistemasIT', 'departamento_id' => $depSistemas->id]);
        $puestoOperador = Puesto::firstOrCreate(['nombre' => 'Inspector Técnico de Descargas'], ['nivel_acceso' => 'operador', 'departamento_id' => $depOperadores->id]);
        $puestoMuestreador = Puesto::firstOrCreate(['nombre' => 'Técnico Muestreador de Laboratorio'], ['nivel_acceso' => 'operador', 'departamento_id' => $depOperadores->id]);

        // 👤 Cuentas de Personal Base (Admin / Pedro)
        User::firstOrCreate(
            ['usuario' => 'admin'],
            [
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
            ]
        );

        User::firstOrCreate(
            ['usuario' => 'pedro.enriquez'],
            [
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
            ]
        );
    }
}
