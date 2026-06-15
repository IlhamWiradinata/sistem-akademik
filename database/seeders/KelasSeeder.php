<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua jurusan dari tabel jurusan
        $jurusanList = DB::table('jurusan')->get();

        $data = [];
        $tahunAjaran = '2025/2026';

        // Tingkatan kelas
        $tingkatan = ['X', 'XI', 'XII'];

        foreach ($jurusanList as $jurusan) {
            foreach ($tingkatan as $tingkat) {
                for ($i = 1; $i <= 2; $i++) { // Dua kelas per tingkat
                    $data[] = [
                        'nama_kelas' => $tingkat . ' ' . $jurusan->kode_jurusan . ' ' . $i,
                        'jurusan_id' => $jurusan->id,
                        'wali_kelas_id' => null, // Bisa diisi nanti
                        'tahun_ajaran' => $tahunAjaran,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        DB::table('kelas')->insert($data);
    }
}
