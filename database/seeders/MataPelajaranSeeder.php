<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MataPelajaranSeeder extends Seeder
{
    public function run()
    {
        // Mata pelajaran untuk jurusan RPL
        $mata_pelajaran = [
            ['kode_mapel' => 'RPL001', 'nama_mapel' => 'Pemrograman Web', 'kelompok' => 'Produktif', 'jam_pelajaran' => 4, 'guru_id' => 1],
            ['kode_mapel' => 'RPL002', 'nama_mapel' => 'Basis Data', 'kelompok' => 'Produktif', 'jam_pelajaran' => 4, 'guru_id' => 1],
            ['kode_mapel' => 'RPL003', 'nama_mapel' => 'PBO', 'kelompok' => 'Produktif', 'jam_pelajaran' => 4, 'guru_id' => 1],
            ['kode_mapel' => 'RPL004', 'nama_mapel' => 'Mobile Programming', 'kelompok' => 'Produktif', 'jam_pelajaran' => 4, 'guru_id' => 1],
            ['kode_mapel' => 'RPL005', 'nama_mapel' => 'Sistem Operasi', 'kelompok' => 'Adaptif', 'jam_pelajaran' => 2, 'guru_id' => 1],
            ['kode_mapel' => 'RPL006', 'nama_mapel' => 'Jaringan Dasar', 'kelompok' => 'Adaptif', 'jam_pelajaran' => 2, 'guru_id' => 1],
        ];

        DB::table('mata_pelajarans')->insert($mata_pelajaran);
    }
}
