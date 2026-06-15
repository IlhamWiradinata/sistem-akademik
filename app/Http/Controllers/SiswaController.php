<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Siswa;
use App\Models\Jurusan;
use App\Models\JadwalKelas;
use App\Models\KelasSiswa;
use App\Models\Nilai;
use App\Models\Kelas;
use App\Models\Kehadiran;
use Carbon\Carbon;

class SiswaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profile = $user->siswaProfile;

        if (!$profile) {
            return view('Siswa.dashboard', [
                'user' => $user,
                'profile' => null,
                'kelasAktif' => null,
                'semester' => '-',
                'jumlahMapel' => 0,
                'rataRata' => 0,
                'kehadiran' => (object)['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0],
                'targetPertemuan' => 0,
                'persentaseKehadiran' => 0,
                'nilaiList' => collect(),
                'nilaiTertinggi' => 0,
                'nilaiTerendah' => 0,
                'sumberNilaiTertinggi' => '',
                'sumberNilaiTerendah' => '',
                'rekapBulanan' => [],
                'persentase' => 0,
                'jadwalHariIni' => collect()
            ]);
        }

        // **AMBIL KELAS DARI TABEL KELAS_SISWA**
        $kelasAktif = DB::table('kelas_siswa')
            ->join('kelas', 'kelas_siswa.kelas_id', '=', 'kelas.id')
            ->where('kelas_siswa.siswa_id', $profile->id)
            ->orderBy('kelas_siswa.tahun_ajaran', 'desc')
            ->orderBy('kelas_siswa.semester', 'desc')
            ->select('kelas.*', 'kelas_siswa.semester', 'kelas_siswa.tahun_ajaran')
            ->first();

        if (!$kelasAktif) {
            // Jika tidak ada kelas, buat objek kosong
            $kelasAktif = (object)[
                'nama_kelas' => 'Belum ada kelas',
                'semester' => '-',
                'tahun_ajaran' => '-'
            ];
            $semester = '-';
            $tahunAjaran = null;
        } else {
            // Ambil semester dari hasil query
            $semester = $kelasAktif->semester ?? '-';
            $tahunAjaran = $kelasAktif->tahun_ajaran ?? null;
        }

        // **AMBIL DATA NILAI**
        $nilaiList = collect();

        if ($profile->nis) {
            $nilaiList = Nilai::where('nis', $profile->nis)
                ->when($semester != '-', function($query) use ($semester) {
                    return $query->where('semester', $semester);
                })
                ->when(isset($tahunAjaran), function($query) use ($tahunAjaran) {
                    return $query->where('tahun_ajaran', $tahunAjaran);
                })
                ->with('mapel')
                ->get();
        }

        // Hitung jumlah mata pelajaran & rata-rata nilai
        $jumlahMapel = $nilaiList->count();
        $rataRata = $nilaiList->avg('rata_rata') ?? 0;

        // **HITUNG NILAI TERTINGGI DAN TERENDAH DARI SEMUA KOMPONEN**
        $semuaNilai = collect();
        $sumberNilaiTertinggi = '';
        $sumberNilaiTerendah = '';

        foreach ($nilaiList as $item) {
            // Tambahkan nilai tugas jika ada
            if ($item->nilai_tugas > 0) {
                $semuaNilai->push([
                    'nilai' => $item->nilai_tugas,
                    'sumber' => 'Tugas',
                    'mapel' => $item->mapel->nama_mapel ?? 'N/A'
                ]);
            }

            // Tambahkan nilai praktikum jika ada
            if ($item->nilai_praktikum > 0) {
                $semuaNilai->push([
                    'nilai' => $item->nilai_praktikum,
                    'sumber' => 'Praktikum',
                    'mapel' => $item->mapel->nama_mapel ?? 'N/A'
                ]);
            }

            // Tambahkan nilai UTS jika ada
            if ($item->nilai_uts > 0) {
                $semuaNilai->push([
                    'nilai' => $item->nilai_uts,
                    'sumber' => 'UTS',
                    'mapel' => $item->mapel->nama_mapel ?? 'N/A'
                ]);
            }

            // Tambahkan nilai UAS jika ada
            if ($item->nilai_uas > 0) {
                $semuaNilai->push([
                    'nilai' => $item->nilai_uas,
                    'sumber' => 'UAS',
                    'mapel' => $item->mapel->nama_mapel ?? 'N/A'
                ]);
            }
        }

        // Hitung nilai tertinggi dan terendah
        if ($semuaNilai->count() > 0) {
            $nilaiTertinggiData = $semuaNilai->sortByDesc('nilai')->first();
            $nilaiTerendahData = $semuaNilai->where('nilai', '>', 0)->sortBy('nilai')->first();

            $nilaiTertinggi = $nilaiTertinggiData['nilai'] ?? 0;
            $sumberNilaiTertinggi = $nilaiTertinggiData['sumber'] ?? '';

            $nilaiTerendah = $nilaiTerendahData['nilai'] ?? 0;
            $sumberNilaiTerendah = $nilaiTerendahData['sumber'] ?? '';
        } else {
            $nilaiTertinggi = 0;
            $nilaiTerendah = 0;
        }

        // ============================================
        // PERHITUNGAN KEHADIRAN HARIAN (REGULER)
        // ============================================

        // 1. Tentukan target pertemuan per bulan
        $targetHariPerBulan = 20; // Bisa disesuaikan

        // 2. Hitung total target semester
        $targetPertemuan = $targetHariPerBulan * 6; // 120 hari per semester

        // 3. Ambil data kehadiran harian untuk semester ini
        $kehadiranSemester = Kehadiran::where('siswa_id', $profile->id)
            ->when($semester != '-', function($query) use ($semester) {
                return $query->where('semester', $semester);
            })
            ->when($tahunAjaran, function($query) use ($tahunAjaran) {
                return $query->where('tahun_ajaran', $tahunAjaran);
            })
            ->orderBy('tanggal', 'asc')
            ->get();

        // 4. Hitung statistik kehadiran
        $kehadiran = (object)[
            'hadir' => $kehadiranSemester->where('status', 'hadir')->count(),
            'izin'  => $kehadiranSemester->where('status', 'izin')->count(),
            'sakit' => $kehadiranSemester->where('status', 'sakit')->count(),
            'alpha' => $kehadiranSemester->where('status', 'alpa')->count(),
        ];

        // 5. Rekap per bulan
        $rekapBulanan = [];
        if ($kehadiranSemester->count() > 0) {
            $kehadiranByMonth = $kehadiranSemester->groupBy(function($item) {
                return \Carbon\Carbon::parse($item->tanggal)->format('Y-m'); // Group by tahun-bulan
            });

            foreach ($kehadiranByMonth as $yearMonth => $kehadiranBulan) {
                $bulan = \Carbon\Carbon::parse($yearMonth . '-01')->translatedFormat('F Y');

                $rekapBulanan[] = [
                    'bulan' => $bulan,
                    'tahun_bulan' => $yearMonth,
                    'hadir' => $kehadiranBulan->where('status', 'hadir')->count(),
                    'izin' => $kehadiranBulan->where('status', 'izin')->count(),
                    'sakit' => $kehadiranBulan->where('status', 'sakit')->count(),
                    'alpha' => $kehadiranBulan->where('status', 'alpa')->count(),
                    'total' => $kehadiranBulan->count(),
                    'target' => $targetHariPerBulan,
                    'persentase' => $targetHariPerBulan > 0 ?
                        round(($kehadiranBulan->where('status', 'hadir')->count() / $targetHariPerBulan) * 100, 1) : 0
                ];
            }
        }

        // 6. Hitung persentase kehadiran semester
        $totalKehadiranAktual = $kehadiran->hadir + $kehadiran->izin + $kehadiran->sakit + $kehadiran->alpha;
        $persentaseKehadiran = $targetPertemuan > 0 ?
            round(($kehadiran->hadir / $targetPertemuan) * 100, 1) : 0;

        // 7. Hitung persentase berdasarkan hari hadir vs target
        $persentaseHadirVsTarget = $targetPertemuan > 0 ?
            round(($kehadiran->hadir / $targetPertemuan) * 100, 1) : 0;

        // Persentase untuk chart lama (jika masih digunakan)
        $persentase = $totalKehadiranAktual > 0 ?
            round(($kehadiran->hadir / $totalKehadiranAktual) * 100, 1) : 0;

        $jadwalHariIni = collect();
        if ($kelasAktif && isset($kelasAktif->id)) {
            $hariIni = Carbon::now()->format('l');
            $hariIniIndonesia = $this->convertHariEnToIndonesia($hariIni);
            $jadwalHariIni = JadwalKelas::with(['mapel', 'guru.user'])
                ->where('kelas_id', $kelasAktif->id)
                ->where('hari', $hariIniIndonesia)
                ->orderBy('jam_mulai')
                ->get();
        }

        return view('Siswa.dashboard', compact(
            'user',
            'profile',
            'kelasAktif',
            'semester',
            'jumlahMapel',
            'rataRata',
            'kehadiran',
            'targetPertemuan',
            'persentaseKehadiran',
            'persentaseHadirVsTarget',
            'rekapBulanan',
            'nilaiList',
            'nilaiTertinggi',
            'nilaiTerendah',
            'sumberNilaiTertinggi',
            'sumberNilaiTerendah',
            'persentase',
            'jadwalHariIni'
        ));
    }

    public function profile()
    {
        $user = Auth::user();

        // Pastikan profil siswa tersedia
        $profile = Siswa::firstOrCreate(
            ['user_id' => $user->id],
            ['nis' => '', 'kelas_id' => null, 'jurusan_id' => null]
        );

        $kelasList = Kelas::all();
        $jurusanList = Jurusan::all();

        return view('Siswa.profile', compact('profile','user','kelasList','jurusanList'));
    }

    /**
     * Update data profil siswa
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $profile = Siswa::with(['kelas', 'jurusan'])
                ->where('user_id', $user->id)
                ->firstOrFail();

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nis'          => 'required|string|max:50',
            'kelas_id'      => 'required|exists:kelas,id',
            'jurusan_id'    => 'nullable|exists:jurusan,id',
            'no_hp'         => 'nullable|string|max:20',
            'alamat'        => 'nullable|string|max:255',
            'tempat_lahir'  => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'nama_ayah'     => 'nullable|string|max:255',
            'no_hp_ayah'    => 'nullable|string|max:20',
            'nama_ibu'      => 'nullable|string|max:255',
            'no_hp_ibu'     => 'nullable|string|max:20',
            'photo'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // === Update data user (hanya name dan email) ===
        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        // === Upload foto profil ===
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
            $profile->photo = $path;
        }

        // === Update data profil siswa ===
        $profile->update([
            'nis'          => $validated['nis'],
            'kelas_id'      => $validated['kelas_id'],
            'jurusan_id'    => $validated['jurusan_id'],
            'no_hp'         => $validated['no_hp'] ?? '',
            'alamat'        => $validated['alamat'] ?? '',
            'tempat_lahir'  => $validated['tempat_lahir'] ?? '',
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'nama_ayah'     => $validated['nama_ayah'] ?? '',
            'no_hp_ayah'    => $validated['no_hp_ayah'] ?? '',
            'nama_ibu'      => $validated['nama_ibu'] ?? '',
            'no_hp_ibu'     => $validated['no_hp_ibu'] ?? '',
            'photo'         => $profile->photo ?? $profile->photo,
        ]);

        return redirect()->route('ProfileSiswa')->with('success', 'Profil siswa berhasil diperbarui.');
    }

    public function ubahPassword()
    {
        return view('Siswa.ubahpassword');
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

    public function lihatNilai(Request $request)
    {
        $user = Auth::user();
        $siswa = Siswa::with(['user', 'jurusan'])->where('user_id', $user->id)->firstOrFail();

        // **AMBIL SEMUA KELAS DARI TABEL KELAS_SISWA BESERTA JURUSAN**
        $kelasList = DB::table('kelas_siswa')
            ->join('kelas', 'kelas_siswa.kelas_id', '=', 'kelas.id')
            ->leftJoin('jurusan', 'kelas.jurusan_id', '=', 'jurusan.id')
            ->where('kelas_siswa.siswa_id', $siswa->id)
            ->select('kelas.*', 'kelas_siswa.semester', 'kelas_siswa.tahun_ajaran', 'jurusan.nama_jurusan')
            ->orderBy('kelas_siswa.tahun_ajaran', 'desc')
            ->orderBy('kelas_siswa.semester', 'desc')
            ->get();

        $kelasTerakhir = $kelasList->first();

        // Ambil semua semester unik
        $semesterList = $kelasList->pluck('semester')->unique()->values();

        // Ambil kelas dan semester terakhir sebagai default
        $kelasDefault = $kelasTerakhir;
        $semesterDefault = $kelasTerakhir->semester ?? null;
        $tahunAjaranDefault = $kelasTerakhir->tahun_ajaran ?? null;

        // Filter dari request atau gunakan default
        $kelasFilter = $request->kelas ?? ($kelasDefault->id ?? null);
        $semesterFilter = $request->semester ?? $semesterDefault;
        $tahunAjaranFilter = $request->tahun_ajaran ?? $tahunAjaranDefault;

        // **QUERY NILAI - HANYA nis**
        $nilai = collect();

        if ($siswa->nis) {
            $nilai = Nilai::with('mapel')
                ->where('nis', $siswa->nis)
                ->when($semesterFilter, function($query) use ($semesterFilter) {
                    return $query->where('semester', $semesterFilter);
                })
                ->when($tahunAjaranFilter, function($query) use ($tahunAjaranFilter) {
                    return $query->where('tahun_ajaran', $tahunAjaranFilter);
                })
                ->orderBy('id_mata_pelajaran')
                ->get();
        }

        // Jika tidak ada data dengan model, coba query langsung
        if ($nilai->isEmpty() && $siswa->nis) {
            $nilai = DB::table('nilai')
                ->join('mata_pelajarans', 'nilai.id_mata_pelajaran', '=', 'mata_pelajarans.id')
                ->where('nilai.nis', $siswa->nis)
                ->when($semesterFilter, function($query) use ($semesterFilter) {
                    return $query->where('nilai.semester', $semesterFilter);
                })
                ->when($tahunAjaranFilter, function($query) use ($tahunAjaranFilter) {
                    return $query->where('nilai.tahun_ajaran', $tahunAjaranFilter);
                })
                ->select('nilai.*', 'mata_pelajarans.nama_mapel')
                ->orderBy('nilai.id_mata_pelajaran')
                ->get();
        }

        // **HITUNG STATISTIK DARI SEMUA KOMPONEN NILAI**
        $jumlahMapel = $nilai->count();
        $rataRata = $nilai->avg('rata_rata') ?? 0;

        // **1. NILAI TERTINGGI DARI SEMUA KOMPONEN**
        $nilaiTertinggi = 0;
        $sumberNilaiTertinggi = '';

        // Cari nilai tertinggi dari semua komponen
        $nilaiTertinggiTugas = $nilai->max('nilai_tugas') ?? 0;
        $nilaiTertinggiPraktikum = $nilai->max('nilai_praktikum') ?? 0;
        $nilaiTertinggiUTS = $nilai->max('nilai_uts') ?? 0;
        $nilaiTertinggiUAS = $nilai->max('nilai_uas') ?? 0;

        // Tentukan mana yang tertinggi
        $nilaiTertinggi = max($nilaiTertinggiTugas, $nilaiTertinggiPraktikum, $nilaiTertinggiUTS, $nilaiTertinggiUAS);

        // Tentukan sumber nilai tertinggi
        if ($nilaiTertinggi == $nilaiTertinggiTugas && $nilaiTertinggi > 0) {
            $sumberNilaiTertinggi = 'Tugas';
        } elseif ($nilaiTertinggi == $nilaiTertinggiPraktikum && $nilaiTertinggi > 0) {
            $sumberNilaiTertinggi = 'Praktikum';
        } elseif ($nilaiTertinggi == $nilaiTertinggiUTS && $nilaiTertinggi > 0) {
            $sumberNilaiTertinggi = 'UTS';
        } elseif ($nilaiTertinggi == $nilaiTertinggiUAS && $nilaiTertinggi > 0) {
            $sumberNilaiTertinggi = 'UAS';
        }

        // **2. NILAI TERENDAH DARI SEMUA KOMPONEN (yang tidak nol)**
        $nilaiTerendah = 100; // Mulai dari maksimum
        $sumberNilaiTerendah = '';

        // Filter nilai yang lebih dari 0 (nilai valid)
        $nilaiValid = $nilai->filter(function($item) {
            return ($item->nilai_tugas ?? 0) > 0 ||
                ($item->nilai_praktikum ?? 0) > 0 ||
                ($item->nilai_uts ?? 0) > 0 ||
                ($item->nilai_uas ?? 0) > 0;
        });

        if ($nilaiValid->count() > 0) {
            // Cari nilai terendah dari komponen yang ada
            $nilaiTerendahTugas = $nilaiValid->where('nilai_tugas', '>', 0)->min('nilai_tugas') ?? 100;
            $nilaiTerendahPraktikum = $nilaiValid->where('nilai_praktikum', '>', 0)->min('nilai_praktikum') ?? 100;
            $nilaiTerendahUTS = $nilaiValid->where('nilai_uts', '>', 0)->min('nilai_uts') ?? 100;
            $nilaiTerendahUAS = $nilaiValid->where('nilai_uas', '>', 0)->min('nilai_uas') ?? 100;

            // Tentukan mana yang terendah
            $nilaiTerendah = min($nilaiTerendahTugas, $nilaiTerendahPraktikum, $nilaiTerendahUTS, $nilaiTerendahUAS);

            // Tentukan sumber nilai terendah
            if ($nilaiTerendah == $nilaiTerendahTugas && $nilaiTerendah < 100) {
                $sumberNilaiTerendah = 'Tugas';
            } elseif ($nilaiTerendah == $nilaiTerendahPraktikum && $nilaiTerendah < 100) {
                $sumberNilaiTerendah = 'Praktikum';
            } elseif ($nilaiTerendah == $nilaiTerendahUTS && $nilaiTerendah < 100) {
                $sumberNilaiTerendah = 'UTS';
            } elseif ($nilaiTerendah == $nilaiTerendahUAS && $nilaiTerendah < 100) {
                $sumberNilaiTerendah = 'UAS';
            }
        } else {
            $nilaiTerendah = 0;
        }

        // Tambahkan tahun ajaran list untuk filter
        $tahunAjaranList = $kelasList->pluck('tahun_ajaran')->unique()->values();

        return view('Siswa.lihatdata.datanilai', compact(
            'siswa',
            'nilai',
            'kelasList',
            'kelasTerakhir',
            'semesterList',
            'tahunAjaranList',
            'kelasFilter',
            'semesterFilter',
            'tahunAjaranFilter',
            'jumlahMapel',
            'rataRata',
            'nilaiTertinggi',
            'nilaiTerendah',
            'sumberNilaiTertinggi',
            'sumberNilaiTerendah'
        ));
    }

    public function lihatKehadiran(Request $request)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return back()->with('error', 'Data siswa tidak ditemukan.');
        }

        // **PERUBAHAN: Ambil kelas dari tabel kelas_siswa**
        $kelasList = DB::table('kelas_siswa')
            ->join('kelas', 'kelas_siswa.kelas_id', '=', 'kelas.id')
            ->where('kelas_siswa.siswa_id', $siswa->id)
            ->select('kelas.*', 'kelas_siswa.semester', 'kelas_siswa.tahun_ajaran')
            ->orderBy('kelas_siswa.tahun_ajaran', 'desc')
            ->orderBy('kelas_siswa.semester', 'desc')
            ->get();

        $kelasTerakhir = $kelasList->first();

        // Ambil data bulan & tahun dari request
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        // **PERUBAHAN: Tambah filter semester dan tahun ajaran**
        $semesterFilter = $request->semester ?? ($kelasTerakhir->semester ?? null);
        $tahunAjaranFilter = $request->tahun_ajaran ?? ($kelasTerakhir->tahun_ajaran ?? null);

        // Query kehadiran dengan filter lengkap
        $kehadiranQuery = Kehadiran::where('siswa_id', $siswa->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        if ($semesterFilter) {
            $kehadiranQuery->where('semester', $semesterFilter);
        }
        if ($tahunAjaranFilter) {
            $kehadiranQuery->where('tahun_ajaran', $tahunAjaranFilter);
        }

        $kehadiran = $kehadiranQuery->orderBy('tanggal', 'asc')->get();
        $kehadiranByDate = $kehadiran->keyBy('tanggal');

        // List bulan untuk dropdown
        $bulanList = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',   '05' => 'Mei',      '06' => 'Juni',
            '07' => 'Juli',    '08' => 'Agustus',  '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];

        // Tahun dari 2020–2030
        $tahunList = range(date('Y') - 2, date('Y') + 2);

        // Ambil semester unik untuk dropdown
        $semesterList = $kelasList->pluck('semester')->unique()->values();
        $tahunAjaranList = $kelasList->pluck('tahun_ajaran')->unique()->values();

        // Hitung statistik
        $statistik = [
            'hadir' => $kehadiran->where('status', 'hadir')->count(),
            'izin' => $kehadiran->where('status', 'izin')->count(),
            'sakit' => $kehadiran->where('status', 'sakit')->count(),
            'alpha' => $kehadiran->where('status', 'alpa')->count(),
        ];

        return view('Siswa.lihatdata.datakehadiran', compact(
            'siswa', 'kehadiran', 'kehadiranByDate', 'kelasTerakhir',
            'bulan', 'tahun', 'bulanList', 'tahunList',
            'semesterList', 'tahunAjaranList', 'semesterFilter', 'tahunAjaranFilter',
            'statistik'
        ));
    }

public function lihatJadwal(Request $request)
{
    $user = Auth::user();
    $profile = $user->siswaProfile;
    $siswa = Siswa::where('user_id', $user->id)->firstOrFail();

    // AMBIL DATA KELAS DARI KELAS_SISWA DENGAN RELASI LENGKAP
    $kelasSiswa = KelasSiswa::with(['kelas' => function($query) {
            $query->with(['jurusan', 'guru.user']);
        }])
        ->where('siswa_id', $siswa->id)
        ->orderBy('tahun_ajaran', 'desc')
        ->orderBy('semester', 'desc')
        ->get();

    if ($kelasSiswa->isEmpty()) {
        return back()->with('error', 'Siswa tidak terdaftar di kelas manapun.');
    }

    $kelasList = DB::table('kelas_siswa')
            ->join('kelas', 'kelas_siswa.kelas_id', '=', 'kelas.id')
            ->where('kelas_siswa.siswa_id', $profile->id)
            ->select('kelas.*', 'kelas_siswa.semester', 'kelas_siswa.tahun_ajaran')
            ->orderBy('kelas_siswa.tahun_ajaran', 'desc')
            ->orderBy('kelas_siswa.semester', 'desc')
            ->get();

    $kelasTerakhir = $kelasList->first();

    $waliKelas = '-';
    if ($kelasTerakhir) {
        $kelasDetail = Kelas::with('waliKelas.user')->find($kelasTerakhir->id);
        $waliKelas = $kelasDetail->waliKelas->user->name ?? '-';
    }

    // BUAT KELAS LIST DARI DATA YANG DIPEROLEH
    $kelasList = collect();
    foreach ($kelasSiswa as $ks) {
        $kelas = $ks->kelas;
        if ($kelas) {
            $kelas->semester = $ks->semester;
            $kelas->tahun_ajaran = $ks->tahun_ajaran;
            $kelasList->push($kelas);
        }
    }

    $kelasTerakhir = $kelasList->first();

    // DEBUG: CEK DATA KELAS TERAKHIR
    // dd([
    //     'kelasTerakhir' => $kelasTerakhir,
    //     'nama_kelas' => $kelasTerakhir->nama_kelas ?? 'NULL',
    //     'tingkat_kelas' => $kelasTerakhir->tingkat_kelas ?? 'NULL',
    //     'jurusan' => $kelasTerakhir->jurusan ?? 'NULL',
    //     'guru' => $kelasTerakhir->guru ?? 'NULL',
    // ]);

    $kelasFilter = $request->kelas ?? $kelasTerakhir->id ?? null;
    $hariFilter = $request->hari ?? '';

    $kelasSelected = $kelasList->firstWhere('id', $kelasFilter);

    if (!$kelasSelected) {
        return back()->with('error', 'Anda tidak memiliki akses ke kelas tersebut.');
    }

    // KELAS DEFAULT UNTUK CARD STATISTIK - GUNAKAN DATA LENGKAP
    $kelasDefault = $kelasSelected;

    // Query jadwal pelajaran
    $jadwalQuery = JadwalKelas::with(['mapel', 'guru.user'])
        ->where('kelas_id', $kelasFilter);

    if ($hariFilter) {
        $jadwalQuery->where('hari', $hariFilter);
    }

    $jadwal = $jadwalQuery->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
        ->orderBy('jam_mulai', 'asc')
        ->get();

    // Jadwal hari ini
    $hariIni = Carbon::now()->format('l');
    $hariIniIndian = $this->convertHariEnToIndonesia($hariIni);

    $jadwalHariIni = JadwalKelas::with(['mapel', 'guru.user'])
        ->where('kelas_id', $kelasFilter)
        ->where('hari', $hariIniIndian)
        ->orderBy('jam_mulai', 'asc')
        ->get();

    return view('Siswa.lihatdata.datamapel', compact(
        'siswa',
        'jadwal',
        'jadwalHariIni',
        'kelasTerakhir',
        'kelasList',
        'waliKelas',
        'kelasList',
        'kelasTerakhir',
        'kelasSelected',
        'kelasDefault',
        'kelasFilter',
        'hariFilter'
    ));
}

    /**
     * Konversi nama hari dari English ke Indonesia
     */
    private function convertHariEnToIndonesia($hariEn)
    {
        $hariMap = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu',
        ];

        return $hariMap[$hariEn] ?? '';
    }
}
