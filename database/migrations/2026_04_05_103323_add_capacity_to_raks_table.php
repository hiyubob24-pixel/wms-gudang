<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('raks', function (Blueprint $table) {
            if (!Schema::hasColumn('raks', 'capacity')) {
                // Menambah kolom kapasitas (default 0, silakan isi di database nanti)
                $table->integer('capacity')->default(0)->after('bagian');
            }
        });
    }

    public function down(): void {
        // Kolom capacity sekarang sudah menjadi bagian dari skema awal tabel raks.
    }
};
