<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LaporanAkademik;

class LaporanAkademikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LaporanAkademik::create([
            'nis' => 21552011428, // pastikan siswa ID 1 ada di tabel siswa
            'kelas_id' => 1, // pastikan kelas ID 1 ada di tabel kelas
            'semester' => 'Ganjil',
            'tahun_ajaran' => '2024/2025',
            'catatan_akademik' => 'Siswa menunjukkan peningkatan signifikan dalam memahami materi pembelajaran dan mampu mengerjakan tugas tepat waktu.',
            'catatan_sikap' => 'Siswa memiliki sikap yang baik, disiplin, serta menunjukkan rasa tanggung jawab dalam kegiatan belajar.',
            'kesimpulan' => 'Secara keseluruhan, siswa memiliki kompetensi akademik yang baik dan menunjukkan perkembangan yang positif.',
            'rekomendasi' => 'Siswa disarankan untuk terus mempertahankan kedisiplinan dan meningkatkan latihan mandiri di rumah.',
        ]);
    }
}
