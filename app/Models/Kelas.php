<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'jurusan_id',
        'wali_kelas_id',
        'tahun_ajaran'
    ];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }

    public function kelasSiswa()
    {
        return $this->hasMany(KelasSiswa::class);
    }

    public function siswa()
    {
        return $this->belongsToMany(Siswa::class, 'kelas_siswa')
            ->withPivot(['semester', 'tahun_ajaran'])
            ->withTimestamps();
    }
    
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }
}



