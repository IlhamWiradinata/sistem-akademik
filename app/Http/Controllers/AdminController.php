<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Administrator;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Jurusan;
use App\Models\Kehadiran;

class AdminController extends Controller
{
    function index()
    {
        $user = Auth::user();
        $profile = $user->adminProfile;

        // Total Statistik
        // Hitung Tahun Ajaran dan Semester Aktif
        $tahunSekarang = date('Y');
        $bulanSekarang = date('m');

        $tahunAjaranAktif = Kelas::latest('tahun_ajaran')->value('tahun_ajaran');
        $kelasAktif = Kelas::where('tahun_ajaran', $tahunAjaranAktif)->pluck('id');

        // Tahun Ajaran
        if ($bulanSekarang >= 7) {
            $tahunAjaranAktif = $tahunSekarang . '/' . ($tahunSekarang + 1);
        } else {
            $tahunAjaranAktif = ($tahunSekarang - 1) . '/' . $tahunSekarang;
        }
        // Semester Aktif
        $semesterAktif = ($bulanSekarang >= 7 && $bulanSekarang <= 12) ? 'Ganjil' : 'Genap';

        // Data statistik lainnya
        $totalSiswa = Siswa::count();
        $totalGuru = Guru::count();
        $totalKelas = $kelasAktif->count();
        $totalMapel = MataPelajaran::count();
        $totalJurusan = Jurusan::count();

        // Jumlah Siswa per Jurusan
        $siswaPerJurusan = Siswa::select('jurusan.nama_jurusan', DB::raw('COUNT(siswa.id) as total'))
            ->join('jurusan', 'siswa.jurusan_id', '=', 'jurusan.id')
            ->groupBy('jurusan.id', 'jurusan.nama_jurusan')
            ->orderBy('total', 'desc')
            ->get();

        $siswaPerTingkat = DB::table('kelas_siswa')
            ->join('kelas', 'kelas_siswa.kelas_id', '=', 'kelas.id')
            ->select(
                DB::raw("CASE
                    WHEN kelas.nama_kelas LIKE 'XII%' THEN 'XII'
                    WHEN kelas.nama_kelas LIKE 'XI%' THEN 'XI'
                    WHEN kelas.nama_kelas LIKE 'X%' THEN 'X'
                    ELSE 'Lainnya'
                END as tingkat"),
                DB::raw('COUNT(DISTINCT kelas_siswa.siswa_id) as total')
            )
            ->whereIn('kelas_siswa.id', function ($q) {
                $q->select(DB::raw('MAX(id)'))
                ->from('kelas_siswa')
                ->groupBy('siswa_id');
            })
            ->groupBy('tingkat')
            ->pluck('total', 'tingkat')
            ->toArray();

        // Data Kehadiran - PERBAIKAN: Tambahkan variabel kehadiran
        $kehadiran = [
            'hadir' => Kehadiran::where('status', 'hadir')->count(),
            'izin' => Kehadiran::where('status', 'izin')->count(),
            'sakit' => Kehadiran::where('status', 'sakit')->count(),
            'alpha' => Kehadiran::where('status', 'alpa')->count(),
        ];

        // Hitung persentase kehadiran (contoh)
        $totalHadir = Kehadiran::where('status', 'hadir')->count();
        $totalAbsen = Kehadiran::where('status', '!=', 'hadir')->count();
        $totalKehadiran = $totalHadir + $totalAbsen;
        $persentase = $totalKehadiran > 0 ? ($totalHadir / $totalKehadiran) * 100 : 0;

        // Persentase Kehadiran per Kelas (untuk pie/bar chart)
        $kehadiranPerKelas = DB::table('kehadiran')
            ->join('kelas_siswa', 'kehadiran.siswa_id', '=', 'kelas_siswa.siswa_id')
            ->join('kelas', 'kelas_siswa.kelas_id', '=', 'kelas.id')
            ->where('kelas.tahun_ajaran', $tahunAjaranAktif)
            ->select(
                'kelas.nama_kelas',
                DB::raw("ROUND(SUM(CASE WHEN kehadiran.status = 'hadir' THEN 1 ELSE 0 END) / COUNT(kehadiran.id) * 100, 1) as persentase")
            )
            ->groupBy('kelas.id', 'kelas.nama_kelas')
            ->orderBy('kelas.nama_kelas')
            ->get();

        return view('Admin.dashboard', compact(
            'user',
            'totalSiswa',
            'totalGuru',
            'totalKelas',
            'totalMapel',
            'totalJurusan',
            'tahunAjaranAktif',
            'semesterAktif',
            'siswaPerJurusan',
            'siswaPerTingkat',
            'kehadiran',
            'kehadiranPerKelas',
            'persentase'
        ));
    }

    public function profile()
    {
        $user = Auth::user();

        // Pastikan profil siswa tersedia
        $profile = Administrator::firstOrCreate(
            ['user_id' => $user->id],
            ['nip' => '', 'jabatan' => '']
        );

        return view('Admin.profile', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $profile = Administrator::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nip'           => 'required|string|max:50',
            'jabatan'       => 'required|string|max:100',
            'no_hp'         => 'nullable|string|max:20',
            'alamat'        => 'nullable|string|max:255',
            'photo'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // === Update data user (hanya name dan email) ===
        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        // === Upload foto profil (disimpan di tabel siswa_profiles) ===
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
            $profile->photo = $path;
        }

        // === Update data profil siswa ===
        $profile->update([
            'nip'           => $validated['nip'],
            'jabatan'       => $validated['jabatan'],
            'no_hp'         => $validated['no_hp'] ?? '',
            'alamat'        => $validated['alamat'] ?? '',
            'photo'         => $profile->photo ?? $profile->photo,
        ]);

        return redirect()->route('ProfileAdmin')->with('success', 'Profil admin berhasil diperbarui.');
    }

    public function ubahPassword()
    {
        return view('Admin.ubahpassword');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Kata sandi berhasil diubah.');
    }
}
