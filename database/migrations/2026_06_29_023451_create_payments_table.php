<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relasi ke tabel invoices
            $table->foreignUuid('invoice_id')->constrained('invoices')->cascadeOnDelete();

            // Relasi ke kasir/admin yang menerima pembayaran (Tabel users)
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();

            // Detail Pembayaran
            $table->string('payment_number')->unique(); // Nomor kuitansi (contoh: PAY-202606-001)
            $table->decimal('amount', 15, 2); // Nominal yang dibayarkan saat ini
            $table->date('payment_date');
            $table->string('payment_method')->default('Tunai'); // Tunai, Transfer Bank, dll
            $table->string('reference_number')->nullable(); // Bukti transfer jika ada
            $table->text('notes')->nullable(); // Catatan tambahan

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
