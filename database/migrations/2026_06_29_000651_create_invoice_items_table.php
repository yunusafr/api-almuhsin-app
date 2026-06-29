<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relasi ke tabel invoices
            $table->foreignUuid('invoice_id')->constrained('invoices')->cascadeOnDelete();

            // Jenis tagihan yang disepakati
            $table->enum('type', [
                'SPP',
                'DAFTAR_ULANG_BARU',
                'DAFTAR_ULANG_LAMA',
                'SPP_PKL',
                'INSIDENTAL',
                'TUNGGAKAN_LAMA'
            ]);

            $table->string('description')->nullable(); // Keterangan teks (misal: "SPP Juli")
            $table->bigInteger('amount'); // Nominal rupiah per item

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
