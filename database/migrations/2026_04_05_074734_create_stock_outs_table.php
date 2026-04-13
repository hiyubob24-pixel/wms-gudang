<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_outs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('rak_id')->constrained();
            $table->integer('quantity');
            $table->datetime('date_time');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('stock_outs');
    }
};