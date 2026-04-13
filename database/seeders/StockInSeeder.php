<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Rak;
use App\Models\Stock;
use App\Models\StockIn;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class StockInSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Nonaktifkan relasi, bersihkan tabel riwayat dan stok fisik
        Schema::disableForeignKeyConstraints();
        StockIn::truncate();
        Stock::truncate();
        Schema::enableForeignKeyConstraints();

        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->warn('Data Produk kosong. Jalankan ProductSeeder terlebih dahulu.');
            return;
        }

        // 2. Loop semua produk untuk dibuatkan transaksi barang masuk
        foreach ($products as $product) {
            
            // LOGIKA UTAMA: Cari rak VIP yang namanya spesifik memuat nama produk ini
            $rak = Rak::where('name', 'like', '%' . $product->name . '%')->first();

            // LOGIKA FALLBACK: Jika tidak ada rak khusus, baru gunakan sistem zona acak
            if (!$rak) {
                $targetZone = 'Fast Moving'; // Default
                
                if ($product->sku) {
                    if (str_contains($product->sku, 'SLW')) $targetZone = 'Slow Moving';
                    if (str_contains($product->sku, 'PAC')) $targetZone = 'Packaging';
                    if (str_contains($product->sku, 'CHE')) $targetZone = 'Bahan Kimia';
                    if (str_contains($product->sku, 'COL')) $targetZone = 'Cooling Room';
                } else {
                    if (str_contains($product->name, 'Lakban')) $targetZone = 'Packaging';
                    if (str_contains($product->name, 'Sabun')) $targetZone = 'Bahan Kimia';
                }

                $rak = Rak::where('bagian', $targetZone)->inRandomOrder()->first();
            }

            // 3. Jika rak (baik VIP maupun Fallback) ditemukan, eksekusi transaksi
            if ($rak) {
                // Generate data acak: jumlah barang (50-500) dan tanggal (dalam 30 hari terakhir)
                $randomQty = rand(50, 500);
                $randomDate = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 24));

                // A. Catat ke riwayat transaksi masuk
                StockIn::create([
                    'product_id' => $product->id,
                    'rak_id'     => $rak->id,
                    'quantity'   => $randomQty,
                    'date_time'  => $randomDate,
                    'created_by' => 1, // Asumsi User ID 1 adalah Admin
                ]);

                // B. Sinkronisasi dengan stok aktual (Gudang)
                $stock = Stock::where('product_id', $product->id)
                              ->where('rak_id', $rak->id)
                              ->first();

                if ($stock) {
                    $stock->quantity += $randomQty;
                    $stock->save();
                } else {
                    Stock::create([
                        'product_id' => $product->id,
                        'rak_id'     => $rak->id,
                        'quantity'   => $randomQty,
                    ]);
                }
            }
        }
    }
}