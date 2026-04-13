<?php

namespace Database\Seeders;

use App\Models\Rak;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema; // Wajib ditambahkan

class RakSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Matikan pengecekan relasi database sementara
        Schema::disableForeignKeyConstraints();

        // 2. Bersihkan tabel dengan aman
        Rak::truncate();

        // 3. Nyalakan kembali pengecekan relasi database
        Schema::enableForeignKeyConstraints();

        $racksData = [];
        
        // Pemetaan Zona untuk 10 Rak (Nomor Rak 1-10)
        $zones = [
            1 => 'Fast Moving', 2 => 'Fast Moving', 3 => 'Fast Moving',
            4 => 'Slow Moving', 5 => 'Slow Moving',
            6 => 'Packaging', 7 => 'Packaging',
            8 => 'Bahan Kimia',
            9 => 'Cooling Room', 10 => 'Cooling Room'
        ];

        // Pemetaan Bahan Baku per Zona (Tingkat 1 [Terberat] s.d Tingkat 5 [Teringan])
        $materials = [
            'Fast Moving' => ['Tepung Terigu (Zak 50kg)', 'Gula Pasir (Zak 50kg)', 'Minyak Sawit (Jerigen)', 'Garam Industri', 'Bahan Kering Curah'],
            'Slow Moving' => ['Pewarna Karamel (Drum)', 'Perisa Sintetis (Galon)', 'Baking Powder', 'Ragi Kering', 'Aditif Serbuk Teringan'],
            'Packaging'   => ['Kardus Master Box', 'Kardus Inner', 'Plastik Roll OPP', 'Botol Kaca/PET', 'Label & Lakban'],
            'Bahan Kimia' => ['Klorin Bubuk', 'Cairan Pembersih Mesin', 'Disinfektan Lantai', 'Alkohol Industri', 'Sabun Cuci Tangan'],
            'Cooling Room'=> ['Mentega / Butter (Blok)', 'Susu Segar (Drum)', 'Enzim Cair', 'Keju Olahan', 'Cokelat Compound']
        ];

        // Eksekusi generator data matriks (10 Rak x 5 Tingkat)
        for ($i = 1; $i <= 10; $i++) {
            for ($lvl = 1; $lvl <= 5; $lvl++) {
                $zone = $zones[$i];
                $materialName = $materials[$zone][$lvl - 1]; 
                
                $racksData[] = [
                    'nomor_rak' => (string)$i,
                    'tingkat'   => (string)$lvl,
                    'bagian'    => $zone,
                    'name'      => 'Rak Bahan ' . $materialName,
                    'created_at'=> now(),
                    'updated_at'=> now(),
                ];
            }
        }

        // Eksekusi query massal
        Rak::insert($racksData);
    }
}