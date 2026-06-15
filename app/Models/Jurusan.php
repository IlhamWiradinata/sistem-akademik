<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database
     */
    protected $table = 'jurusan';
    protected $fillable = [
        'kode_jurusan',
        'nama_jurusan',
        'keterangan',
    ];

        public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }


}
