<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi (Tambah kolom).
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Tambahkan kolom tingkat setelah rombel
            $table->string('tingkat')->nullable()->after('rombel');
        });
    }

    /**
     * Kembalikan seperti semula jika di-rollback (Hapus kolom).
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Drop kolom jika migrasi di-rollback
            $table->dropColumn('tingkat');
        });
    }
};
