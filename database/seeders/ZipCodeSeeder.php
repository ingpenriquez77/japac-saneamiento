<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ZipCode;

class ZipCodeSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = storage_path('app/Codigos_Postales.csv');

        if (!file_exists($filePath)) {
            $this->command->warn("⚠️ No se encontró el archivo CSV en 'storage/app/Codigos_Postales.csv'. Brincando...");
            return;
        }

        $file = fopen($filePath, 'r');
        $batch = [];
        fgetcsv($file, 0, ',', '"'); // Saltar encabezado

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
    }
}
