<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            
            // Kolom yang sebelumnya tertinggal
            $table->string('nomor_rak')->nullable();
            $table->string('tingkat')->nullable();
            $table->string('bagian')->nullable();
            $table->integer('capacity')->default(0); // Kapasitas maksimal rak
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raks');
    }
};