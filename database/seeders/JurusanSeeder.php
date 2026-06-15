<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('jurusan')->insert([
            [
                'kode_jurusan' => 'RPL',
                'nama_jurusan' => 'Rekayasa Perangkat Lunak',
                'keterangan' => 'Jurusan yang mempelajari pengembangan perangkat lunak dan aplikasi.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_jurusan' => 'TKJ',
                'nama_jurusan' => 'Teknik Komputer dan Jaringan',
                'keterangan' => 'Jurusan yang mempelajari jaringan komputer dan perangkat keras.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_jurusan' => 'AKL',
                'nama_jurusan' => 'Akuntansi dan Keuangan Lembaga',
                'keterangan' => 'Jurusan yang berfokus pada bidang akuntansi dan keuangan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_jurusan' => 'TP',
                'nama_jurusan' => 'Teknik Pemesinan',
                'keterangan' => 'Jurusan yang mempelajari teknik produksi dan permesinan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_jurusan' => 'TKRO',
                'nama_jurusan' => 'Teknik Kendaraan Ringan Otomotif',
                'keterangan' => 'Jurusan yang mempelajari perawatan dan perbaikan kendaraan ringan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
