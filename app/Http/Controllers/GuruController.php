<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\JadwalKelas;
use App\Models\Kehadiran;
use Carbon\Carbon;

class GuruController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $guru = Guru::where('user_id', $user->id)->firstOrFail();

        $jadwalGuru = JadwalKelas::with(['kelas', 'mapel'])
            ->where('guru_id', $guru->id)
            ->get();

        $tahunAjaranAktif = $this->getTahunAjaranAktif();
        $semesterAktif = $this->getSemesterAktif();

        if ($jadwalGuru->isEmpty()) {
            return view('Guru.dashboard', [
                'guru' => $guru,
                'statistik' => $this->emptyStatistik($tahunAjaranAktif, $semesterAktif),
                'daftarKelasGuru' => collect([])
            ]);
        }

        $kelasIds = $jadwalGuru->pluck('kelas_id')->unique();
        $kelasList = Kelas::whereIn('id', $kelasIds)
            ->where('tahun_ajaran', $tahunAjaranAktif)
            ->get();

        $mapelIds = $jadwalGuru->pluck('id_mata_pelajaran')->unique();

        $siswaIds = DB::table('kelas_siswa')
            ->whereIn('kelas_id', $kelasIds)
            ->where('tahun_ajaran', $tahunAjaranAktif)
            ->pluck('siswa_id');
        $siswaList = Siswa::whereIn('id', $siswaIds)->get();
        $nisList = $siswaList->pluck('nis')->toArray();

        $statistik = [];

        $statistik['total_kelas'] = $kelasList->count();
        $statistik['total_siswa'] = $siswaList->count();
        $statistik['total_mapel'] = $mapelIds->count();
        $statistik['total_nilai'] = Nilai::where('nip', $guru->nip)
            ->where('tahun_ajaran', $tahunAjaranAktif)
            ->count();

        // Kehadiran hari ini
        $hariIni = Carbon::now()->toDateString();
        $kehadiranHariIni = Kehadiran::whereIn('siswa_id', $siswaIds)
            ->where('tanggal', $hariIni)
            ->where('tahun_ajaran', $tahunAjaranAktif)
            ->get();
        $statistik['kehadiran_hari_ini'] = [
            'hadir' => $kehadiranHariIni->where('status', 'hadir')->count(),
            'izin'  => $kehadiranHariIni->where('status', 'izin')->count(),
            'sakit' => $kehadiranHariIni->where('status', 'sakit')->count(),
            'alpa'  => $kehadiranHariIni->where('status', 'alpa')->count(),
        ];

        // Kehadiran bulan ini
        $awalBulan = Carbon::now()->startOfMonth()->toDateString();
        $akhirBulan = Carbon::now()->endOfMonth()->toDateString();
        $kehadiranBulanIni = Kehadiran::whereIn('siswa_id', $siswaIds)
            ->whereBetween('tanggal', [$awalBulan, $akhirBulan])
            ->where('tahun_ajaran', $tahunAjaranAktif)
            ->get();
        $totalHadirBulan = $kehadiranBulanIni->where('status', 'hadir')->count();
        $totalHariKerja = $this->hitungHariKerja($awalBulan, $akhirBulan);
        $totalKehadiranMaksimal = $siswaList->count() * $totalHariKerja;
        $statistik['persentase_kehadiran_bulan_ini'] = $totalKehadiranMaksimal > 0
            ? round(($totalHadirBulan / $totalKehadiranMaksimal) * 100)
            : 0;

        // Rekapitulasi kehadiran semester
        $kehadiranSemester = Kehadiran::whereIn('siswa_id', $siswaIds)
            ->where('tahun_ajaran', $tahunAjaranAktif)
            ->where('semester', $semesterAktif)
            ->get();
        $statistik['rekapitulasi_kehadiran'] = [
            'hadir' => $kehadiranSemester->where('status', 'hadir')->count(),
            'izin'  => $kehadiranSemester->where('status', 'izin')->count(),
            'sakit' => $kehadiranSemester->where('status', 'sakit')->count(),
            'alpa'  => $kehadiranSemester->where('status', 'alpa')->count(),
        ];

        // Distribusi grade semester
        $nilaiSemester = Nilai::where('nip', $guru->nip)
            ->where('tahun_ajaran', $tahunAjaranAktif)
            ->where('semester', $semesterAktif)
            ->whereIn('nis', $nisList)
            ->get();
        $statistik['distribusi_grade'] = [
            'A' => $nilaiSemester->where('grade', 'A')->count(),
            'B' => $nilaiSemester->where('grade', 'B')->count(),
            'C' => $nilaiSemester->where('grade', 'C')->count(),
            'D' => $nilaiSemester->where('grade', 'D')->count(),
            'E' => $nilaiSemester->where('grade', 'E')->count(),
        ];

        // Kelas & mapel diampu
        $statistik['kelas_dan_mapel'] = [];
        foreach ($jadwalGuru->unique('kelas_id') as $jadwal) {
            $kelas = $jadwal->kelas;
            if (!$kelas || $kelas->tahun_ajaran != $tahunAjaranAktif) continue;
            $mapelDiKelas = $jadwalGuru->where('kelas_id', $kelas->id)->pluck('mapel')->unique();
            foreach ($mapelDiKelas as $mapel) {
                if (!$mapel) continue;
                $siswaKelasIds = DB::table('kelas_siswa')
                    ->where('kelas_id', $kelas->id)
                    ->where('tahun_ajaran', $tahunAjaranAktif)
                    ->pluck('siswa_id');
                $jumlahSiswa = Siswa::whereIn('id', $siswaKelasIds)->count();
                $nisKelas = Siswa::whereIn('id', $siswaKelasIds)->pluck('nis')->toArray();
                $nilai = Nilai::where('nip', $guru->nip)
                    ->where('id_mata_pelajaran', $mapel->id)
                    ->where('tahun_ajaran', $tahunAjaranAktif)
                    ->where('semester', $semesterAktif)
                    ->whereIn('nis', $nisKelas)
                    ->get();
                $statistik['kelas_dan_mapel'][] = [
                    'kelas' => $kelas->nama_kelas,
                    'mapel' => $mapel->nama_mapel,
                    'jumlah_siswa' => $jumlahSiswa,
                    'nilai_tersimpan' => $nilai->count(),
                    'rata_rata_nilai' => $nilai->count() > 0 ? round($nilai->avg('rata_rata'), 2) : 0,
                ];
            }
        }

        $statistik['tahun_ajaran_aktif'] = $tahunAjaranAktif;
        $statistik['semester_aktif'] = $semesterAktif;

        $daftarKelasGuru = $kelasList;

        return view('Guru.dashboard', compact('guru', 'statistik', 'daftarKelasGuru'));
    }

    public function filterData(Request $request)
    {
        $user = Auth::user();
        $guru = Guru::where('user_id', $user->id)->firstOrFail();

        $semester = $request->input('semester', $this->getSemesterAktif());
        $kelasId = $request->input('kelas_id');
        $tahunAjaranAktif = $this->getTahunAjaranAktif();

        $kelasIds = JadwalKelas::where('guru_id', $guru->id)->pluck('kelas_id')->unique();

        $siswaQuery = DB::table('kelas_siswa')
            ->whereIn('kelas_id', $kelasIds)
            ->where('tahun_ajaran', $tahunAjaranAktif);
        if ($kelasId) {
            $siswaQuery->where('kelas_id', $kelasId);
        }
        $siswaIds = $siswaQuery->pluck('siswa_id');

        $nisList = DB::table('siswa')->whereIn('id', $siswaIds)->pluck('nis')->toArray();

        $kehadiran = Kehadiran::whereIn('siswa_id', $siswaIds)
            ->where('tahun_ajaran', $tahunAjaranAktif)
            ->where('semester', $semester)
            ->get();

        $rekap = [
            'hadir' => $kehadiran->where('status', 'hadir')->count(),
            'izin'  => $kehadiran->where('status', 'izin')->count(),
            'sakit' => $kehadiran->where('status', 'sakit')->count(),
            'alpa'  => $kehadiran->where('status', 'alpa')->count(),
        ];

        $nilai = Nilai::where('nip', $guru->nip)
            ->where('tahun_ajaran', $tahunAjaranAktif)
            ->where('semester', $semester)
            ->whereIn('nis', $nisList)
            ->get();

        $grade = [
            'A' => $nilai->where('grade', 'A')->count(),
            'B' => $nilai->where('grade', 'B')->count(),
            'C' => $nilai->where('grade', 'C')->count(),
            'D' => $nilai->where('grade', 'D')->count(),
            'E' => $nilai->where('grade', 'E')->count(),
        ];

        return response()->json([
            'rekap' => $rekap,
            'grade' => $grade,
            'totalGrade' => array_sum($grade),
        ]);
    }

    public function filterDataKelas(Request $request)
    {
        $user = Auth::user();
        $guru = Guru::where('user_id', $user->id)->firstOrFail();

        $semester = $request->input('semester', $this->getSemesterAktif());
        $tahunAjaranAktif = $this->getTahunAjaranAktif();

        $jadwalGuru = JadwalKelas::with(['kelas', 'mapel'])
            ->where('guru_id', $guru->id)
            ->get();

        if ($jadwalGuru->isEmpty()) {
            return response()->json([]);
        }

        $kelasIds = $jadwalGuru->pluck('kelas_id')->unique();
        $kelasList = Kelas::whereIn('id', $kelasIds)
            ->where('tahun_ajaran', $tahunAjaranAktif)
            ->get();

        $kelasDanMapel = [];

        foreach ($jadwalGuru->unique('kelas_id') as $jadwal) {
            $kelas = $jadwal->kelas;
            if (!$kelas || $kelas->tahun_ajaran != $tahunAjaranAktif) continue;

            $mapelDiKelas = $jadwalGuru->where('kelas_id', $kelas->id)->pluck('mapel')->unique();

            foreach ($mapelDiKelas as $mapel) {
                if (!$mapel) continue;

                $siswaKelasIds = DB::table('kelas_siswa')
                    ->where('kelas_id', $kelas->id)
                    ->where('tahun_ajaran', $tahunAjaranAktif)
                    ->pluck('siswa_id');

                $jumlahSiswa = Siswa::whereIn('id', $siswaKelasIds)->count();
                $nisKelas = Siswa::whereIn('id', $siswaKelasIds)->pluck('nis')->toArray();

                $nilai = Nilai::where('nip', $guru->nip)
                    ->where('id_mata_pelajaran', $mapel->id)
                    ->where('tahun_ajaran', $tahunAjaranAktif)
                    ->where('semester', $semester)
                    ->whereIn('nis', $nisKelas)
                    ->get();

                $kelasDanMapel[] = [
                    'kelas' => $kelas->nama_kelas,
                    'mapel' => $mapel->nama_mapel,
                    'jumlah_siswa' => $jumlahSiswa,
                    'nilai_tersimpan' => $nilai->count(),
                    'rata_rata_nilai' => $nilai->count() > 0 ? round($nilai->avg('rata_rata'), 2) : 0,
                ];
            }
        }

        return response()->json($kelasDanMapel);
    }

    private function getTahunAjaranAktif()
    {
        return date('n') >= 7 ? date('Y') . '/' . (date('Y') + 1) : (date('Y') - 1) . '/' . date('Y');
    }

    private function getSemesterAktif()
    {
        return date('n') >= 7 ? 'Ganjil' : 'Genap';
    }

    private function hitungHariKerja($awal, $akhir)
    {
        $hari = 0;
        $current = Carbon::parse($awal);
        $end = Carbon::parse($akhir);
        while ($current <= $end) {
            if ($current->dayOfWeek != 0 && $current->dayOfWeek != 6) $hari++;
            $current->addDay();
        }
        return $hari;
    }

    private function emptyStatistik($tahunAjaran, $semester)
    {
        return [
            'total_kelas' => 0,
            'total_siswa' => 0,
            'total_mapel' => 0,
            'total_nilai' => 0,
            'kehadiran_hari_ini' => ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0],
            'persentase_kehadiran_bulan_ini' => 0,
            'rekapitulasi_kehadiran' => ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0],
            'distribusi_grade' => ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0],
            'kelas_dan_mapel' => [],
            'tahun_ajaran_aktif' => $tahunAjaran,
            'semester_aktif' => $semester,
        ];
    }

    public function profile()
    {
        $user = Auth::user();

        // Pastikan profil siswa tersedia
        $profile = Guru::firstOrCreate(
            ['user_id' => $user->id],
            ['nip' => '', 'bidang_keahlian' => '']
        );

        return view('Guru.profile', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $profile = Guru::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nip'               => 'required|string|max:50',
            'bidang_keahlian'    => 'required|string|max:100',
            'no_hp'             => 'nullable|string|max:20',
            'alamat'            => 'nullable|string|max:255',
            'photo'             => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
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

        if (Nilai::where('nip', $profile->nip)->exists() && $validated['nip'] != $profile->nip) {
            return back()->with('error', 'nip tidak dapat diubah karena sudah digunakan pada tabel nilai.');
        }

        // === Update data profil siswa ===
        $profile->update([
            'nip'           => $validated['nip'],
            'bidang_keahlian'=> $validated['bidang_keahlian'],
            'no_hp'         => $validated['no_hp'] ?? '',
            'alamat'        => $validated['alamat'] ?? '',
            'photo'         => $profile->photo ?? $profile->photo,
        ]);

        return redirect()->route('ProfileGuru')->with('success', 'Profil admin berhasil diperbarui.');
    }

    public function ubahPassword()
    {
        return view('Guru.ubahpassword');
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
