<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laporan_akademiks', function (Blueprint $table) {
            $table->id();

            // Relasi siswa & kelas
            $table->string('nis');
            $table->foreign('nis')->references('nis')->on('siswa')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');

            // Informasi akademik umum
            $table->string('semester', 10);
            $table->string('tahun_ajaran', 20);

            // Catatan akademik dan sikap
            $table->text('catatan_akademik')->nullable();
            $table->text('catatan_sikap')->nullable();

            // Kesimpulan dan rekomendasi
            $table->text('kesimpulan')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_akademiks');
    }
};
