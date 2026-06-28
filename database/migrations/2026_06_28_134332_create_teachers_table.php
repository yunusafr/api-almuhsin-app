<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            // 1. Primary key menggunakan UUID
            $table->uuid('id')->primary();

            // 2. Relasi ke tabel users (Foreign Key) menggunakan UUID
            // Jika tabel users Anda masih pakai BigInteger biasa, ganti 'foreignUuid' menjadi 'foreignId'
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();

            // 3. Kolom biodata ustadz
            $table->string('name');
            $table->enum('gender', ['L', 'P']); // L = Laki-laki, P = Perempuan
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
