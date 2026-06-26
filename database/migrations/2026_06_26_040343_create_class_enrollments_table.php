<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_enrollments', function (Blueprint $table) {
            // Menggunakan UUID sebagai Primary Key
            $table->uuid('id')->primary();

            // Menghubungkan 3 unsur penting (Santri, Kelas, Tahun Ajaran) menggunakan UUID
            $table->foreignUuid('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignUuid('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignUuid('academic_year_id')->constrained('academic_years')->cascadeOnDelete();

            $table->string('status')->default('aktif'); // aktif, mutasi, lulus
            $table->timestamps();

            // Proteksi: Santri tidak boleh terplot di dua kelas berbeda pada tahun ajaran yang sama
            $table->unique(['academic_year_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_enrollments');
    }
};
