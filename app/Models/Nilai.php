<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Nilai extends Model
{
    use HasFactory;

    protected $table = 'nilai'; // nama tabel tidak dalam bentuk jamak

    protected $fillable = [
        'nis',
        'nip',
        'nuptk',
        'id_mata_pelajaran',
        'nilai_tugas',
        'nilai_praktikum',
        'nilai_uts',
        'nilai_uas',
        'sikap',
        'grade',
        'rata_rata',
        'semester',
        'tahun_ajaran'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'nis');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id', 'nuptk');
    }

    public function mapel()
    {
        return $this->belongsTo(MataPelajaran::class, 'id_mata_pelajaran', 'id');
    }
}
