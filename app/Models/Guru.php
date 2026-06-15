<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;
    protected $table = 'guru';
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'nip',
        'bidang_keahlian',
        'no_hp',
        'alamat',
        'photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }

    public function jadwalKelas()
    {
        return $this->hasMany(JadwalKelas::class, 'guru_id', 'id');
    }

    // Di Model Guru.php
    public function kelasYangDiajar()
    {
        // Kelas dimana guru adalah wali kelas
        $kelasWali = $this->kelas()->get();

        // Kelas dimana guru mengajar mata pelajaran
        $kelasMengajar = Kelas::whereHas('mataPelajaran', function($query) {
            $query->where('guru_id', $this->id);
        })->get();

        return $kelasWali->merge($kelasMengajar)->unique('id');
    }

    public function mataPelajaran()
    {
        return $this->hasMany(MataPelajaran::class, 'guru_id');
    }
}
