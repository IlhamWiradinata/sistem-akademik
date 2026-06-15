<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Siswa;
use App\Models\Kelas;

class LaporanAkademik extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'kelas_id',
        'semester',
        'tahun_ajaran',
        'catatan_akademik',
        'catatan_sikap',
        'kesimpulan',
        'rekomendasi',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nis', 'nis'); // bukan 'siswa_id'
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}
