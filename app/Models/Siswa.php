<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $fillable = [
        'user_id',
        'nis',
        'kelas_id',
        'jurusan_id',
        'jenis_kelamin',
        'no_hp',
        'alamat',
        'tempat_lahir',
        'tanggal_lahir',
        'nama_ayah',
        'no_hp_ayah',
        'nama_ibu',
        'no_hp_ibu',
        'photo',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    public function kelasHistori()
    {
        return $this->hasMany(KelasSiswa::class, 'siswa_id');
    }

    public function kelasAktif()
    {
        return $this->hasOne(KelasSiswa::class, 'siswa_id')
                    ->latest('created_at');
    }

    public function kelasSiswa()
    {
        return $this->hasMany(KelasSiswa::class, 'siswa_id');
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'nis', 'nis');
    }

    // Relasi ke kehadiran
    public function kehadiran()
    {
        return $this->hasMany(Kehadiran::class, 'siswa_id',);
    }

    // Relasi ke laporan akademik
    public function laporanAkademik()
    {
        return $this->hasOne(LaporanAkademik::class, 'nis', 'nis')->latest();
    }

}
