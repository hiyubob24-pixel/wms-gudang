<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Matikan pengecekan relasi untuk mencegah error constraint
        Schema::disableForeignKeyConstraints();

        // 2. Kosongkan tabel produk secara aman
        Product::truncate();

        // 3. Nyalakan kembali perlindungan relasi
        Schema::enableForeignKeyConstraints();

        $products = [
            // Kategori Fast Moving
            ['name' => 'Tepung Terigu (Zak 50kg)', 'sku' => 'RM-FAS-TPG-001'],
            ['name' => 'Gula Pasir (Zak 50kg)', 'sku' => 'RM-FAS-GLA-002'],
            ['name' => 'Minyak Sawit (Jerigen)', 'sku' => 'RM-FAS-MYK-003'],
            ['name' => 'Garam Industri', 'sku' => 'RM-FAS-GRM-004'],
            ['name' => 'Bahan Kering Curah', 'sku' => null], 
            
            // Kategori Slow Moving
            ['name' => 'Pewarna Karamel (Drum)', 'sku' => 'RM-SLW-PWR-005'],
            ['name' => 'Perisa Sintetis (Galon)', 'sku' => 'RM-SLW-PRS-006'],
            ['name' => 'Baking Powder (Pail)', 'sku' => 'RM-SLW-BKP-007'],
            ['name' => 'Ragi Kering', 'sku' => 'RM-SLW-RGI-008'],
            
            // Kategori Packaging
            ['name' => 'Kardus Master Box', 'sku' => 'RM-PAC-KMB-009'],
            ['name' => 'Kardus Inner', 'sku' => 'RM-PAC-INR-010'],
            ['name' => 'Plastik Roll OPP', 'sku' => 'RM-PAC-OPP-011'],
            ['name' => 'Botol Kaca/PET', 'sku' => 'RM-PAC-BTL-012'],
            ['name' => 'Label & Lakban', 'sku' => null], 
            
            // Kategori Bahan Kimia
            ['name' => 'Klorin Bubuk', 'sku' => 'RM-CHE-KLR-013'],
            ['name' => 'Cairan Pembersih Mesin', 'sku' => 'RM-CHE-CLN-014'],
            ['name' => 'Disinfektan Lantai', 'sku' => 'RM-CHE-DSF-015'],
            ['name' => 'Alkohol Industri', 'sku' => 'RM-CHE-ALC-016'],
            ['name' => 'Sabun Cuci Tangan', 'sku' => null], 
            
            // Kategori Cooling Room
            ['name' => 'Mentega / Butter (Blok 15kg)', 'sku' => 'RM-COL-MTG-017'],
            ['name' => 'Susu Segar (Drum)', 'sku' => 'RM-COL-SSU-018'],
            ['name' => 'Enzim Cair Aktif (Botol)', 'sku' => 'RM-COL-ENZ-019'],
            ['name' => 'Keju Olahan', 'sku' => 'RM-COL-KJU-020'],
            ['name' => 'Cokelat Compound', 'sku' => 'RM-COL-CKL-021'],
        ];

        // Tambahkan timestamp untuk setiap baris
        $timestamp = now();
        foreach ($products as &$product) {
            $product['created_at'] = $timestamp;
            $product['updated_at'] = $timestamp;
        }

        // 4. Eksekusi query massal
        Product::insert($products);
    }
}