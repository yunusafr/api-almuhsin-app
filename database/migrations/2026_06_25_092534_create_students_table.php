<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Menggunakan UUID sebagai Primary Key
            $table->string('nis')->unique()->nullable(); // Jembatan ke aplikasi data siswa sebelah
            $table->string('name');
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('rombel')->nullable(); // Rombongan Belajar / Kelas Sementara
            $table->string('status')->default('aktif'); // Status: aktif, keluar, lulus, mutasi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
