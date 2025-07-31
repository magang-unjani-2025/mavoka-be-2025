<?php

use Illuminate\Database\Seeder;
use Database\Seeders\AdminSeeder;
use Database\Seeders\SekolahSeeder;
use Database\Seeders\JurusanSeeder;
use Database\Seeders\SiswaSeeder;
use Database\Seeders\PerusahaanSeeder;
use Database\Seeders\LembagaSeeder;
use Database\Seeders\LowonganMagangSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            SekolahSeeder::class,
            JurusanSeeder::class,
            SiswaSeeder::class,
            PerusahaanSeeder::class,
            LembagaSeeder::class,
            LowonganMagangSeeder::class,
        ]);
    }
}
