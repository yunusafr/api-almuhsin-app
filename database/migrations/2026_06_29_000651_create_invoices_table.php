<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relasi ke tabel students (pastikan tabel students pakai UUID juga)
            $table->foreignUuid('student_id')->constrained('students')->cascadeOnDelete();

            $table->string('invoice_number')->unique(); // Contoh: INV-202606-0001
            $table->bigInteger('total_amount')->default(0); // Total rupiah tagihan
            $table->bigInteger('paid_amount')->default(0);  // Total rupiah yang sudah dibayar
            $table->enum('status', ['UNPAID', 'PARTIAL', 'PAID'])->default('UNPAID');
            $table->date('due_date'); // Tanggal jatuh tempo

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
