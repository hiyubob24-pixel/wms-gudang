<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('raks', function (Blueprint $table) {
            // Beri komentar pada baris ini agar Laravel mengabaikannya
            // $table->dropUnique('raks_nomor_rak_unique'); 
        });
    }

    public function down(): void
    {
        Schema::table('raks', function (Blueprint $table) {
            $table->unique('nomor_rak');
        });
    }
};