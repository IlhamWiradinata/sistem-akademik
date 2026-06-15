<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
        'kelompok',
        'jam_pelajaran',
    ];

    /**
     * Relasi ke model Guru
     */

    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'mapel_id', 'id');
    }
}
