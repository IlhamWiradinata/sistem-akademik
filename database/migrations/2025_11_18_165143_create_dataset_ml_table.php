<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatasetMlTable extends Migration
{
    public function up()
    {
        Schema::create('dataset_ml', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->nullable();
            $table->decimal('rata_nilai', 8, 2)->nullable();
            $table->decimal('kehadiran', 5, 2)->nullable(); // persentase 0-100
            $table->string('kelas')->nullable();
            $table->string('kategori_prestasi')->nullable(); // target: Baik/Cukup/Kurang
            $table->timestamps();
        });

        Schema::create('ml_models', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('decision_tree');
            $table->string('file_path'); // storage path to model.pkl
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ml_models');
        Schema::dropIfExists('dataset_ml');
    }
}
