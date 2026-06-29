<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // Contoh: "SPP Bulanan Normal", "Daftar Ulang Santri Baru"
            $table->enum('invoice_type', ['SPP', 'DAFTAR_ULANG_BARU', 'DAFTAR_ULANG_LAMA', 'SPP_PKL', 'INSIDENTAL', 'TUNGGAKAN_LAMA']);
            $table->bigInteger('default_amount')->default(0); // Nominal default bawaan
            $table->string('default_description')->nullable(); // Keterangan bawaan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_categories');
    }
};
