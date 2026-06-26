<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            // Menggunakan UUID sebagai Primary Key
            $table->uuid('id')->primary();
            $table->string('name'); // Contoh: "VII-A", "Kamar Abu Bakar"
            $table->string('level')->nullable(); // Contoh: "7", "8", "9" (untuk mempermudah filter)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
