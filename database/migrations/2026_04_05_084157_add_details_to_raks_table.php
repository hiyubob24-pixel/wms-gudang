<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('raks', function (Blueprint $table) {
            if (!Schema::hasColumn('raks', 'nomor_rak')) {
                $table->string('nomor_rak')->after('name')->nullable();
            }

            if (!Schema::hasColumn('raks', 'tingkat')) {
                $table->string('tingkat')->after('nomor_rak')->nullable();  // contoh: "Tingkat 1", "Level A"
            }

            if (!Schema::hasColumn('raks', 'bagian')) {
                $table->string('bagian')->after('tingkat')->nullable();     // contoh: "Gudang Utara", "Zona B"
            }

            // Hapus kolom name jika tidak diperlukan lagi, atau biarkan sebagai nama alternatif
        });
    }

    public function down(): void
    {
        // Kolom-kolom ini sekarang sudah menjadi bagian dari skema awal tabel raks.
    }
};
