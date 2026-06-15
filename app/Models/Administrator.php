<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrator extends Model
{
    use HasFactory;
    protected $table = 'administrator';
    protected $fillable = [
        'user_id',
        'nip',
        'name',
        'email',
        'jabatan',
        'no_hp',
        'alamat',
        'photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
