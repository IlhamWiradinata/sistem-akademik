<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Jalankan seeder.
     */
    public function run(): void
    {
        // ===== DATA USERS UTAMA =====
        $users = [
            [
                'name' => 'Ilham Wiradinata',
                'email' => 'admin@gmail.com',
                'role' => 'Administrator',
                'password' => bcrypt('123456'),
            ],
            [
                'name' => 'Tubagus Ilham',
                'email' => 'guru@gmail.com',
                'role' => 'Guru',
                'password' => bcrypt('123456'),
            ],
            [
                'name' => 'Ilham Ramdhan',
                'email' => 'siswa@gmail.com',
                'role' => 'Siswa',
                'password' => bcrypt('123456'),
            ],
        ];

        foreach ($users as $userData) {
            // ===== BUAT ATAU UPDATE USER =====
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'role' => $userData['role'],
                    'password' => $userData['password'],
                ]
            );

            // ===== ROLE BASED PROFILE INSERT / UPDATE =====
            switch ($userData['role']) {
                case 'Administrator':
                    DB::table('administrator')->updateOrInsert(
                        ['user_id' => $user->id],
                        [
                            'nuptk' => '198004252014122006',
                            'jabatan' => 'Staff Kurikulum',
                            'no_hp' => '081234567890',
                            'alamat' => 'Jl. Merdeka No. 1, Bandung',
                            'photo' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                    break;

                case 'Guru':
                    DB::table('guru')->updateOrInsert(
                        ['user_id' => $user->id],
                        [
                            'nuptk' => '198004252014122001',
                            'bidang_keahlian' => 'Produktif RPL',
                            'no_hp' => '081234567891',
                            'alamat' => 'Jl. Pendidikan No. 5, Bandung',
                            'photo' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                    break;

                case 'Siswa':
                    DB::table('siswa')->updateOrInsert(
                        ['user_id' => $user->id],
                        [
                            'nisn' => '21552011428',
                            'kelas_id' => 1,     // pastikan kelas_id=1 ada di tabel kelas
                            'jurusan_id' => 1,   // pastikan jurusan_id=1 ada di tabel jurusan
                            'jenis_kelamin' => 'Laki-laki',
                            'no_hp' => '081234567890',
                            'alamat' => 'Jl. Merdeka No. 123, Bandung',
                            'tempat_lahir' => 'Bandung',
                            'tanggal_lahir' => '2008-05-15',
                            'nama_ayah' => 'Budi Santoso',
                            'no_hp_ayah' => '081234567891',
                            'nama_ibu' => 'Siti Rahmawati',
                            'no_hp_ibu' => '081234567892',
                            'photo' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                    break;
            }
        }
    }
}
