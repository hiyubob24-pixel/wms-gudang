<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Memanggil kelas seeder lain secara berurutan
        $this->call([
            UserSeeder::class,    // 1. Mengeksekusi data user (admin/staff)
            RakSeeder::class,     // 2. Mengeksekusi data lokasi rak gudang
            ProductSeeder::class, // 3. Mengeksekusi data master barang
            StockInSeeder::class, // 4. Mengeksekusi simulasi transaksi barang masuk
        ]);
    }
}