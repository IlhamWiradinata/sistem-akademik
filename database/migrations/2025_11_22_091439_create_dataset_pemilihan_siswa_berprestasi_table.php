<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel untuk menyimpan hasil prediksi Decision Tree
        // Hanya menyimpan score dan kategori hasil analisis
        Schema::create('siswa_berprestasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('siswa_id');
            $table->unsignedBigInteger('kelas_id');
            $table->unsignedBigInteger('jurusan_id');

            // Score dan kategori hasil Decision Tree (data ini diambil dari tabel nilai & kehadiran)
            $table->decimal('score_decision_tree', 5, 2); // Hasil kalkulasi dari nilai yang ada
            $table->string('kategori_prestasi'); // 'sangat_berprestasi', 'berprestasi', 'cukup'

            $table->enum('tingkat', ['umum', 'jurusan'])->default('jurusan'); // Tipe analisis
            $table->string('tahun_akademik')->nullable(); // Tahun akademik analisis

            $table->timestamps();

            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
            $table->foreign('jurusan_id')->references('id')->on('jurusan')->onDelete('cascade');

            // Index untuk query cepat
            $table->index('siswa_id');
            $table->index('jurusan_id');
            $table->index('kategori_prestasi');
            $table->unique(['siswa_id', 'tingkat', 'tahun_akademik']);
        });

        // Tabel untuk menyimpan hasil pemilihan juara per kelas/jurusan
        Schema::create('siswa_juara', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('siswa_id');
            $table->unsignedBigInteger('kelas_id');
            $table->unsignedBigInteger('jurusan_id')->nullable();

            $table->integer('ranking'); // 1, 2, 3, dst
            $table->string('kategori_juara'); // 'juara_umum', 'juara_jurusan', 'juara_kelas'
            $table->decimal('final_score', 5, 2); // Score dari Decision Tree

            $table->string('tahun_akademik'); // Tahun akademik penetapan juara

            $table->timestamps();

            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
            $table->foreign('jurusan_id')->references('id')->on('jurusan')->onDelete('set null');

            // Index
            $table->index('siswa_id');
            $table->index('jurusan_id');
            $table->index('kategori_juara');
            $table->index('tahun_akademik');
        });

        // Tabel untuk menyimpan log/history pemilihan siswa berprestasi
        // Berguna untuk tracking perubahan dan audit trail
        Schema::create('siswa_berprestasi_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('siswa_id');
            $table->unsignedBigInteger('user_id'); // Admin yang melakukan analisis

            $table->string('jenis_analisis'); // 'umum' atau 'jurusan'
            $table->unsignedBigInteger('jurusan_id')->nullable();

            $table->decimal('akurasi_model', 5, 2); // Akurasi model saat itu
            $table->integer('total_dianalisis'); // Total siswa yang dianalisis

            $table->json('parameter_model')->nullable(); // Parameter model yang digunakan
            $table->string('status'); // 'success' atau 'failed'

            $table->timestamps();

            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('jurusan_id')->references('id')->on('jurusan')->onDelete('set null');

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa_berprestasi_logs');
        Schema::dropIfExists('siswa_juara');
        Schema::dropIfExists('siswa_berprestasi');
    }
};
