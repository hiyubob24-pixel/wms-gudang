<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('raks', function (Blueprint $table) {
            // Sistem hanya akan menyuntikkan kolom jika kolom tersebut benar-benar belum ada di database
            if (!Schema::hasColumn('raks', 'nomor_rak')) {
                $table->string('nomor_rak')->nullable()->after('location');
            }
            
            if (!Schema::hasColumn('raks', 'tingkat')) {
                $table->string('tingkat')->nullable()->after('nomor_rak');
            }
            
            if (!Schema::hasColumn('raks', 'bagian')) {
                $table->string('bagian')->nullable()->after('tingkat');
            }
            
            // Ini adalah target utama kita untuk memperbaiki error "Rak Penuh"
            if (!Schema::hasColumn('raks', 'capacity')) {
                $table->integer('capacity')->default(0)->after('bagian');
            }
        });
    }

    public function down(): void
    {
        Schema::table('raks', function (Blueprint $table) {
            if (Schema::hasColumn('raks', 'nomor_rak')) {
                $table->dropColumn('nomor_rak');
            }
            if (Schema::hasColumn('raks', 'tingkat')) {
                $table->dropColumn('tingkat');
            }
            if (Schema::hasColumn('raks', 'bagian')) {
                $table->dropColumn('bagian');
            }
            if (Schema::hasColumn('raks', 'capacity')) {
                $table->dropColumn('capacity');
            }
        });
    }
};