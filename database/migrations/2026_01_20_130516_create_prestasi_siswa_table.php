<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('prestasi_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained()->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained()->onDelete('cascade');
            $table->string('tahun_ajaran');
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->enum('jenis_prestasi', ['ranking_kelas', 'juara_umum']);
            $table->string('tingkat')->nullable()->comment('Hanya untuk juara_umum: X, XI, XII');
            $table->integer('ranking');
            $table->decimal('nilai_rata_rata', 5, 2);
            $table->decimal('persentase_kehadiran', 5, 2);
            $table->decimal('nilai_perilaku', 5, 2);
            $table->decimal('skor_total', 5, 2);
            $table->integer('jumlah_mapel')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();

            // Index untuk performa query
            $table->index(['tahun_ajaran', 'semester', 'jenis_prestasi']);
            $table->index(['kelas_id', 'ranking']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('prestasi_siswa');
    }
};
