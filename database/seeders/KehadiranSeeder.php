<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KehadiranSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kehadiran')->insert([
            [
                'siswa_id' => 1,
                'tanggal' => now()->subDays(3),
                'status' => 'hadir',
                'keterangan' => null,
            ],
            [
                'siswa_id' => 1,
                'tanggal' => now()->subDays(2),
                'status' => 'izin',
                'keterangan' => 'Ada keperluan keluarga',
            ],
            [
                'siswa_id' => 1,
                'tanggal' => now()->subDays(1),
                'status' => 'sakit',
                'keterangan' => 'Demam',
            ],
            [
                'siswa_id' => 1,
                'tanggal' => now(),
                'status' => 'alpa',
                'keterangan' => null,
            ],
        ]);
    }
}
