<?php

use Illuminate\Database\Seeder;
use Database\Seeders\AdminSeeder;
use Database\Seeders\SekolahSeeder;
use Database\Seeders\SiswaSeeder;
use Database\Seeders\PerusahaanSeeder;
use Database\Seeders\LembagaSeeder;
use Database\Seeders\LowonganMagangSeeder;
use Database\Seeders\PelatihanSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            SekolahSeeder::class,
            SiswaSeeder::class,
            PerusahaanSeeder::class,
            LembagaSeeder::class,
            LowonganMagangSeeder::class,
            PelatihanSeeder::class,
        ]);
    }
}
