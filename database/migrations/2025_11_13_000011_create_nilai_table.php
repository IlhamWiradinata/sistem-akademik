<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNilaiTable extends Migration
{
    public function up()
    {
        Schema::create('nilai', function (Blueprint $table) {
            $table->id();

            // Jika 'nis' di tabel siswa adalah STRING
            $table->string('nis');
            $table->foreign('nis')->references('nis')->on('siswa')->onDelete('cascade');

            // Jika 'nip' di tabel guru adalah STRING
            $table->string('nip');
            $table->foreign('nip')->references('nip')->on('guru')->onDelete('cascade');

            $table->foreignId('id_mata_pelajaran')->constrained('mata_pelajarans')->onDelete('cascade');

            $table->integer('nilai_tugas')->nullable();
            $table->integer('nilai_praktikum')->nullable();
            $table->integer('nilai_uts')->nullable();
            $table->integer('nilai_uas')->nullable();
            $table->enum('sikap', ['A', 'B', 'C', 'D', 'E']);
            $table->string('grade', 2)->nullable();
            $table->decimal('rata_rata', 5, 2)->nullable();
            $table->string('semester', 10)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nilai');
    }
}
