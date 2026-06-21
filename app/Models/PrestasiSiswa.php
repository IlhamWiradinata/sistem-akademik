<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestasiSiswa extends Model
{
    use HasFactory;

    protected $table = 'prestasi_siswa';
    protected $fillable = [
        'siswa_id',
        'kelas_id',
        'tahun_ajaran',
        'semester',
        'jenis_prestasi',
        'tingkat',
        'ranking',
        'nilai_rata_rata',
        'persentase_kehadiran',
        'nilai_perilaku',
        'skor_total',
        'kategori_dt',
        'jumlah_mapel',
        'status',
    ];

    protected $casts = [
        'nilai_rata_rata' => 'decimal:2',
        'persentase_kehadiran' => 'decimal:2',
        'nilai_perilaku' => 'decimal:2',
        'skor_total' => 'decimal:2',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
