<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NilaiSeeder extends Seeder
{
    public function run()
    {
        $nis = '21552011428'; // NIS siswa
        $nip = 'GURU001';        // NIP guru dummy

        // Ambil semua mata pelajaran
        $mata_pelajaran = DB::table('mata_pelajarans')->get();

        $data = [];

        foreach ($mata_pelajaran as $mp) {
            $data[] = [
                'nis' => $nis,
                'nip' => $nip,
                'id_mata_pelajaran' => $mp->id,
                'nilai_tugas' => rand(80, 95),
                'nilai_praktikum' => rand(80, 95),
                'nilai_uts' => rand(75, 90),
                'nilai_uas' => rand(80, 95),
                'sikap' => 'B',
                'grade' => 'A',
                'rata_rata' => rand(80, 95),
                'semester' => 'Ganjil',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('nilai')->insert($data);
    }
}
