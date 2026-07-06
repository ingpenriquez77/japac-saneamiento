<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CatalogoSaneamientoSeeder::class,
            ZipCodeSeeder::class,
            EstablecimientoYVisitasSeeder::class,
            CalculoIncumplimientoSeeder::class,
            InicioProcedimientoSeeder::class,
            ResolutivoAdministrativoSeeder::class,
        ]);
    }
}
