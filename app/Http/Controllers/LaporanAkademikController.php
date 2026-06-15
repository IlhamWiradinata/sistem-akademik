<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\KelasSiswa;
use App\Models\Nilai;
use App\Models\Kelas;
use App\Models\Kehadiran;
use App\Models\LaporanAkademik;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanAkademikController extends Controller{

    public function adminIndex(Request $request)
    {
        if ($request->ajax() && $request->has('tahun_ajaran') && !$request->has('semester')) {
        return Kelas::where('tahun_ajaran', $request->tahun_ajaran)
            ->orderBy('nama_kelas')
            ->get(['id', 'nama_kelas']);
        }

        // ===== FILTER INPUT =====
        $tahunAjaran = $request->input('tahun_ajaran');
        $semester    = $request->input('semester', 'Ganjil');

        // ===== LIST TAHUN AJARAN =====
        $listTahunAjaran = Kelas::select('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran', 'desc')
            ->pluck('tahun_ajaran');

        // ===== LIST KELAS SESUAI TAHUN AJARAN =====
        $kelasList = Kelas::when($tahunAjaran, function ($q) use ($tahunAjaran) {
            $q->where('tahun_ajaran', $tahunAjaran);
        })->orderBy('nama_kelas')->get();

        // ===== KELAS AKTIF =====
        $kelasId = $request->input('kelas_id');

        // ===== SISWA SESUAI KELAS (MENGGUNAKAN RELASI MANY-TO-MANY) =====
        $siswaList = collect();
        if ($kelasId && $semester && $tahunAjaran) {
            // Cari siswa yang terdaftar di kelas tertentu dengan semester dan tahun ajaran
            $siswaList = Siswa::whereHas('kelasSiswa', function($q) use ($kelasId, $semester, $tahunAjaran) {
                $q->where('kelas_id', $kelasId)
                ->where('semester', $semester)
                ->where('tahun_ajaran', $tahunAjaran);
            })
            ->with([
                'user',
                'jurusan',
                'laporanAkademik' => function($q) use ($kelasId, $semester) {
                    $q->where('kelas_id', $kelasId)
                    ->where('semester', $semester);
                },
                'nilai' => function($q) use ($semester) {
                    $q->where('semester', $semester);
                },
                'kehadiran'
            ])
            ->get();
            $siswaList->transform(function ($siswa) use ($kelasId, $semester) {

            $hasLaporan = $siswa->laporanAkademik ? true : false;
            $hasNilai   = $siswa->nilai->count() > 0;
            $hasHadir   = $siswa->kehadiran->count() > 0;

            // STATUS SELESAI JIKA SEMUA TERISI
            $siswa->status_laporan = ($hasLaporan && $hasNilai && $hasHadir)
                ? 'selesai'
                : 'belum';

            return $siswa;
        });

        }

        // ===== SISWA AKTIF =====
        $siswaId = $request->input('siswa_id') ?? $siswaList->first()->id ?? null;
        $siswa = Siswa::with('user','jurusan')->find($siswaId);

        // ===== DUMMY OBJECT (ANTI ERROR) =====
        if (!$siswa) {
            $siswa = (object)[
                'id' => 0,
                'nis' => '',
                'user' => (object)['name' => '-'],
                'jurusan' => (object)['nama_jurusan' => '-'],
            ];
        }

        // ===== LAPORAN AKADEMIK =====
        $laporanAkademik = LaporanAkademik::where('nis', $siswa->nis)
            ->where('kelas_id', $kelasId)
            ->where('semester', $semester)
            ->latest()
            ->first() ?? (object)[
                'catatan_akademik' => '',
                'catatan_sikap'    => '',
                'kesimpulan'       => '',
                'rekomendasi'      => '',
            ];

        // ===== STATISTIK NILAI =====
        $nilai = $siswa->id > 0
            ? $siswa->nilai()->where('semester', $semester)->get()
            : collect();
        $totalMapel = $nilai->count();
        $rataRata = $nilai->avg('rata_rata') ?? 0;

        // ===== KEHADIRAN =====
        $kehadiran = Kehadiran::where('siswa_id', $siswa->id)->get();
        $rekapKehadiran = (object)[
            'hadir' => $kehadiran->where('status', 'hadir')->count(),
            'izin'  => $kehadiran->where('status', 'izin')->count(),
            'sakit' => $kehadiran->where('status', 'sakit')->count(),
            'alpha' => $kehadiran->where('status', 'alpa')->count(),
        ];

        $totalHadir = $rekapKehadiran->hadir + $rekapKehadiran->izin + $rekapKehadiran->sakit + $rekapKehadiran->alpha;
        $persentaseHadir = $totalHadir > 0 ? ($rekapKehadiran->hadir / $totalHadir * 100) : 0;

        // ===== KELAS AKTIF SISWA =====
        $kelasAktif = null;
        if ($siswa->id > 0 && $kelasId) {
            $kelasAktif = Kelas::find($kelasId);
        }

        $waliKelas = $kelasAktif ? ($kelasAktif->waliKelas->user->name ?? '-') : '-';

        return view('Admin.laporanakademik', compact(
            'listTahunAjaran',
            'tahunAjaran',
            'kelasList',
            'kelasId',
            'semester',
            'siswaList',
            'siswa',
            'laporanAkademik',
            'nilai',
            'totalMapel',
            'rataRata',
            'rekapKehadiran',
            'persentaseHadir',
            'kelasAktif',
            'waliKelas'
        ));
    }

    public function adminStore(Request $request)
    {
        $data = $request->validate([
            'nis' => 'required',
            'kelas_id' => 'required',
            'semester' => 'required',
            'tahun_ajaran'     => 'required',
            'catatan_akademik' => 'nullable|string',
            'catatan_sikap' => 'nullable|string',
            'kesimpulan' => 'nullable|string',
            'rekomendasi' => 'nullable|string',
        ]);

        LaporanAkademik::updateOrCreate(
            [
                'nis' => $data['nis'],
                'kelas_id' => $data['kelas_id'],
                'semester' => $data['semester'],
                'tahun_ajaran' => $data['tahun_ajaran'],
            ],
            [
                'catatan_akademik' => $data['catatan_akademik'],
                'catatan_sikap' => $data['catatan_sikap'],
                'kesimpulan' => $data['kesimpulan'],
                'rekomendasi' => $data['rekomendasi'],
            ]
        );

        return redirect()->back()->with('success','Laporan akademik berhasil disimpan.');
    }

    public function unduhAdmin(Request $request)
    {
        // =========================
        // VALIDASI INPUT
        // =========================
        $request->validate([
            'nis'         => 'required|exists:siswa,nis',
            'semester'     => 'required|in:Ganjil,Genap',
            'tahun_ajaran' => 'required',
            'kelas_id'     => 'required|exists:kelas,id'
        ]);

        $nis         = $request->nis;
        $semester     = $request->semester;
        $tahunAjaran  = $request->tahun_ajaran;
        $kelasId      = $request->kelas_id;

        // =========================
        // DATA SISWA
        // =========================
        $siswa = Siswa::with(['user', 'jurusan'])
            ->where('nis', $nis)
            ->firstOrFail();

        // =========================
        // KELAS AKTIF
        // =========================
        $kelasAktif = Kelas::with('waliKelas.user')
            ->where('id', $kelasId)
            ->first();

        if (!$kelasAktif) {
            abort(404, 'Kelas tidak ditemukan.');
        }

        $waliKelas = $kelasAktif->waliKelas->user->name ?? '-';

        // =========================
        // LAPORAN AKADEMIK
        // =========================
        $laporanakademik = LaporanAkademik::where([
            'nis'         => $nis,
            'semester'     => $semester,
            'kelas_id'     => $kelasId,
            'tahun_ajaran' => $tahunAjaran,
        ])->first();

        if (!$laporanakademik) {
            $laporanakademik = (object)[
                'catatan_akademik' => 'Belum ada catatan akademik',
                'catatan_sikap'    => 'Belum ada catatan sikap',
                'kesimpulan'       => 'Belum ada kesimpulan',
                'rekomendasi'      => 'Belum ada rekomendasi',
            ];
        }

        // =========================
        // NILAI
        // =========================
        $nilai = Nilai::with('mapel')
            ->where([
                'nis'         => $nis,
                'semester'     => $semester,
                'tahun_ajaran' => $tahunAjaran,
            ])
            ->orderBy('id_mata_pelajaran', 'asc')
            ->get();

        // =========================
        // ABSENSI (PER SEMESTER)
        // =========================
        $absensi = (object)[
            'hadir' => Kehadiran::where('siswa_id', $siswa->id)
                ->where('semester', $semester)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('status', 'hadir')->count(),

            'izin' => Kehadiran::where('siswa_id', $siswa->id)
                ->where('semester', $semester)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('status', 'izin')->count(),

            'sakit' => Kehadiran::where('siswa_id', $siswa->id)
                ->where('semester', $semester)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('status', 'sakit')->count(),

            'alpha' => Kehadiran::where('siswa_id', $siswa->id)
                ->where('semester', $semester)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('status', 'alpa')->count(),
        ];

        // =========================
        // GENERATE PDF
        // =========================
        $pdf = Pdf::loadView(
            'admin.pdf', // Ganti dengan nama file yang benar
            compact(
                'siswa',
                'kelasAktif',
                'waliKelas',
                'laporanakademik',
                'nilai',
                'absensi',
                'semester',
                'tahunAjaran'
            )
        )->setPaper('A4', 'portrait');

        $namaSiswa   = str_replace(' ', '_', $siswa->user->name);
        $tahunAjaranFile = str_replace('/', '-', $tahunAjaran);

        $filename = 'Laporan_Akademik_' .
            $namaSiswa .
            '_Semester_' . $semester .
            '_TA_' . $tahunAjaranFile .
            '.pdf';

        return $pdf->download($filename);
    }

    // Tambahkan method ini di controller sebelum guruIndex
    public function getKelasByTahunAjaran(Request $request)
    {
        $user = Auth::user();
        $guru = Guru::where('user_id', $user->id)->firstOrFail();

        $tahunAjaran = $request->tahun_ajaran;

        $kelasIds = DB::table('jadwal_kelas')
            ->join('kelas', 'jadwal_kelas.kelas_id', '=', 'kelas.id')
            ->where('jadwal_kelas.guru_id', $guru->id)
            ->where('kelas.tahun_ajaran', $tahunAjaran)
            ->distinct()
            ->pluck('jadwal_kelas.kelas_id');

        $kelasList = Kelas::whereIn('id', $kelasIds)
            ->orderBy('nama_kelas')
            ->get(['id', 'nama_kelas']);

        return response()->json($kelasList);
    }

    // Fungsi untuk menentukan grade
    private function tentukanGrade($nilai)
    {
        if ($nilai >= 85) return 'A';
        if ($nilai >= 75) return 'B';
        if ($nilai >= 65) return 'C';
        if ($nilai >= 55) return 'D';
        return 'E';
    }

    private function getKehadiranUntukSemester($siswaId, $semester, $tahunAjaran)
    {
        // Tentukan rentang tanggal untuk semester
        $tanggalRange = $this->getRentangTanggalSemester($semester, $tahunAjaran);

        // Ambil semua kehadiran dalam rentang tanggal semester
        $kehadiranSemester = Kehadiran::where('siswa_id', $siswaId)
            ->whereBetween('tanggal', [$tanggalRange['start'], $tanggalRange['end']])
            ->orderBy('tanggal')
            ->get();

        $pertemuanData = [];
        $utsData = ['status' => '-', 'tanggal' => null];
        $uasData = ['status' => '-', 'tanggal' => null];

        // Pisahkan kehadiran regular, UTS, dan UAS
        $pertemuanCount = 0;
        foreach ($kehadiranSemester as $k) {
            $keterangan = strtoupper(trim($k->keterangan ?? ''));

            // Cek jika ini UTS atau UAS
            if (str_contains($keterangan, 'UTS')) {
                $utsData = [
                    'status' => $this->getStatusShort($k->status),
                    'tanggal' => $k->tanggal
                ];
                continue;
            }

            if (str_contains($keterangan, 'UAS')) {
                $uasData = [
                    'status' => $this->getStatusShort($k->status),
                    'tanggal' => $k->tanggal
                ];
                continue;
            }

            // Kehadiran regular (pertemuan)
            if ($pertemuanCount < 14) {
                $pertemuanData[$pertemuanCount + 1] = [
                    'status' => $this->getStatusShort($k->status),
                    'tanggal' => $k->tanggal
                ];
                $pertemuanCount++;
            }
        }

        // Isi pertemuan yang kosong
        for ($i = $pertemuanCount + 1; $i <= 14; $i++) {
            if (!isset($pertemuanData[$i])) {
                $pertemuanData[$i] = [
                    'status' => '-',
                    'tanggal' => null
                ];
            }
        }

        // Urutkan berdasarkan kunci
        ksort($pertemuanData);

        return [
            'pertemuan' => $pertemuanData,
            'uts' => $utsData,
            'uas' => $uasData
        ];
    }

    private function getRentangTanggalSemester($semester, $tahunAjaran)
    {
        // Parsing tahun ajaran
        $tahunParts = explode('/', $tahunAjaran);
        $tahun1 = intval($tahunParts[0]);
        $tahun2 = isset($tahunParts[1]) ? intval($tahunParts[1]) : $tahun1 + 1;

        if ($semester === 'Ganjil') {
            // Semester Ganjil: 1 Juli - 31 Desember
            return [
                'start' => $tahun1 . '-07-01',
                'end' => $tahun1 . '-12-31'
            ];
        } else {
            // Semester Genap: 1 Januari - 30 Juni
            return [
                'start' => $tahun2 . '-01-01',
                'end' => $tahun2 . '-06-30'
            ];
        }
    }

    private function getStatusShort($status)
    {
        $mapping = [
            'hadir' => 'H',
            'izin' => 'I',
            'sakit' => 'S',
            'alpa' => 'A'
        ];

        return $mapping[strtolower($status)] ?? '-';
    }

    public function guruIndex(Request $request)
    {
        $user = Auth::user();
        $guru = Guru::where('user_id', $user->id)->firstOrFail();

        // ===== FILTER INPUT =====
        $tahunAjaran = $request->input('tahun_ajaran');
        $semester    = $request->input('semester', 'Ganjil');

        // ===== LIST TAHUN AJARAN (dari kelas) =====
        $listTahunAjaran = Kelas::select('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran', 'desc')
            ->pluck('tahun_ajaran');

        // ===== AMBIL KELAS YANG DIAMPU GURU (berdasarkan jadwal_kelas) =====
        $kelasIds = DB::table('jadwal_kelas')
            ->join('kelas', 'jadwal_kelas.kelas_id', '=', 'kelas.id')
            ->where('jadwal_kelas.guru_id', $guru->id)
            ->when($tahunAjaran, function ($q) use ($tahunAjaran) {
                $q->where('kelas.tahun_ajaran', $tahunAjaran);
            })
            ->distinct()
            ->pluck('jadwal_kelas.kelas_id');

        $kelasList = Kelas::whereIn('id', $kelasIds)
            ->orderBy('nama_kelas')
            ->get();

        // ===== KELAS AKTIF =====
        $kelasId = $request->input('kelas_id');

        // ===== MAPEL YANG DIAJAR GURU DI KELAS INI =====
        $mapelIds = collect();
        $mapelNamaList = collect();
        if ($kelasId) {
            $mapelRows = DB::table('jadwal_kelas')
                ->join('mata_pelajarans', 'jadwal_kelas.id_mata_pelajaran', '=', 'mata_pelajarans.id')
                ->where('jadwal_kelas.guru_id', $guru->id)
                ->where('jadwal_kelas.kelas_id', $kelasId)
                ->distinct()
                ->get(['mata_pelajarans.id', 'mata_pelajarans.nama_mapel']);

            $mapelIds      = $mapelRows->pluck('id');
            $mapelNamaList = $mapelRows->pluck('nama_mapel');
        }
        $mataPelajaranGuru = $mapelNamaList->isNotEmpty()
            ? $mapelNamaList->implode(', ')
            : 'Tidak ada mapel di kelas ini';

        // ===== STATISTIK AWAL =====
        $totalSiswa = 0;
        $siswaDenganNilai = 0;
        $siswaDenganKehadiran = 0;
        $laporanLengkap = 0;

        // ===== SISWA SESUAI KELAS =====
        $siswaList = collect();

        if ($kelasId && $semester && $tahunAjaran) {
            // Ambil siswa dari kelas tertentu
            $siswaList = Siswa::whereHas('kelasSiswa', function($q) use ($kelasId, $semester, $tahunAjaran) {
                    $q->where('kelas_id', $kelasId)
                    ->where('semester', $semester)
                    ->where('tahun_ajaran', $tahunAjaran);
                })
                ->with([
                    'user',
                    'jurusan',
                    'nilai' => function($q) use ($semester, $tahunAjaran, $mapelIds) {
                        $q->where('semester', $semester)
                        ->where('tahun_ajaran', $tahunAjaran)
                        ->when($mapelIds->isNotEmpty(), function ($qq) use ($mapelIds) {
                            $qq->whereIn('id_mata_pelajaran', $mapelIds);
                        })
                        ->with('mapel');
                    }
                ])
                ->get();

            $totalSiswa = $siswaList->count();

            // ===== HITUNG DATA SETIAP SISWA =====
            foreach ($siswaList as $siswa) {
                // ===== PERHITUNGAN NILAI (HANYA MAPEL GURU) =====
                $totalNilai = 0;
                $jumlahMapel = $siswa->nilai->count();

                if ($jumlahMapel > 0) {
                    $siswaDenganNilai++;

                    foreach ($siswa->nilai as $nilai) {
                        $totalNilai += $nilai->rata_rata;
                    }
                    $rataRata = $totalNilai / $jumlahMapel;
                    $siswa->rata_rata_nilai = number_format($rataRata, 2);
                    $siswa->grade = $this->tentukanGrade($rataRata);
                } else {
                    $siswa->rata_rata_nilai = '-';
                    $siswa->grade = '-';
                }

                // ===== PERHITUNGAN KEHADIRAN =====
                $kehadiranSemester = $this->getKehadiranUntukSemester($siswa->id, $semester, $tahunAjaran);

                $totalHadir = 0;
                $totalIzin = 0;
                $totalSakit = 0;
                $totalAlpha = 0;
                $totalPertemuan = count($kehadiranSemester['pertemuan']);

                foreach ($kehadiranSemester['pertemuan'] as $pertemuan) {
                    if ($pertemuan['status'] === 'H') {
                        $totalHadir++;
                    } elseif ($pertemuan['status'] === 'I') {
                        $totalIzin++;
                    } elseif ($pertemuan['status'] === 'S') {
                        $totalSakit++;
                    } elseif ($pertemuan['status'] === 'A') {
                        $totalAlpha++;
                    }
                }

                if ($kehadiranSemester['uts']['status'] !== '-') {
                    $totalPertemuan++;
                    if ($kehadiranSemester['uts']['status'] === 'H') $totalHadir++;
                    elseif ($kehadiranSemester['uts']['status'] === 'I') $totalIzin++;
                    elseif ($kehadiranSemester['uts']['status'] === 'S') $totalSakit++;
                    elseif ($kehadiranSemester['uts']['status'] === 'A') $totalAlpha++;
                }

                if ($kehadiranSemester['uas']['status'] !== '-') {
                    $totalPertemuan++;
                    if ($kehadiranSemester['uas']['status'] === 'H') $totalHadir++;
                    elseif ($kehadiranSemester['uas']['status'] === 'I') $totalIzin++;
                    elseif ($kehadiranSemester['uas']['status'] === 'S') $totalSakit++;
                    elseif ($kehadiranSemester['uas']['status'] === 'A') $totalAlpha++;
                }

                if ($totalPertemuan > 0) {
                    $persentaseKehadiran = round(($totalHadir / $totalPertemuan) * 100, 1);
                    $siswaDenganKehadiran++;
                } else {
                    $persentaseKehadiran = 0;
                }

                $siswa->total_hadir = $totalHadir;
                $siswa->total_izin = $totalIzin;
                $siswa->total_sakit = $totalSakit;
                $siswa->total_alpha = $totalAlpha;
                $siswa->total_pertemuan = $totalPertemuan;
                $siswa->persentase_kehadiran = $persentaseKehadiran;

                if ($totalPertemuan > 0) {
                    if ($persentaseKehadiran >= 80) {
                        $siswa->status_kehadiran = 'Baik';
                    } elseif ($persentaseKehadiran >= 60) {
                        $siswa->status_kehadiran = 'Cukup';
                    } else {
                        $siswa->status_kehadiran = 'Kurang';
                    }
                } else {
                    $siswa->status_kehadiran = '-';
                }

                // ===== CEK KELENGKAPAN LAPORAN =====
                $hasNilai = $jumlahMapel > 0;
                $hasKehadiran = $totalPertemuan > 0;

                if ($hasNilai && $hasKehadiran) {
                    $laporanLengkap++;
                }
            }
        }

        return view('Guru.laporanakademik', compact(
            'listTahunAjaran',
            'tahunAjaran',
            'kelasList',
            'kelasId',
            'semester',
            'siswaList',
            'totalSiswa',
            'siswaDenganNilai',
            'siswaDenganKehadiran',
            'laporanLengkap',
            'mataPelajaranGuru'
        ));
    }

    public function detailSiswa($id, Request $request)
    {
        try {
            $user = Auth::user();
            $guru = Guru::where('user_id', $user->id)->first();

            if (!$guru) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data guru tidak ditemukan'
                ], 404);
            }

            $kelasId = $request->kelas_id;

            // ===== MAPEL YANG DIAJAR GURU DI KELAS INI =====
            $mapelIds = DB::table('jadwal_kelas')
                ->where('guru_id', $guru->id)
                ->where('kelas_id', $kelasId)
                ->pluck('id_mata_pelajaran');

            $siswa = Siswa::with([
                'user',
                'jurusan',
                'nilai' => function($q) use ($request, $mapelIds) {
                    $q->where('semester', $request->semester)
                    ->where('tahun_ajaran', $request->tahun_ajaran)
                    ->when($mapelIds->isNotEmpty(), function ($qq) use ($mapelIds) {
                        $qq->whereIn('id_mata_pelajaran', $mapelIds);
                    })
                    ->with('mapel');
                }
            ])->findOrFail($id);

            // ===== KEHADIRAN =====
            $kehadiranSemester = $this->getKehadiranUntukSemester($siswa->id, $request->semester, $request->tahun_ajaran);

            $totalPertemuan = count($kehadiranSemester['pertemuan']);
            $totalHadir = 0;
            $totalIzin = 0;
            $totalSakit = 0;
            $totalAlpha = 0;

            foreach ($kehadiranSemester['pertemuan'] as $pertemuan) {
                if ($pertemuan['status'] === 'H') $totalHadir++;
                elseif ($pertemuan['status'] === 'I') $totalIzin++;
                elseif ($pertemuan['status'] === 'S') $totalSakit++;
                elseif ($pertemuan['status'] === 'A') $totalAlpha++;
            }

            if ($kehadiranSemester['uts']['status'] !== '-') {
                $totalPertemuan++;
                if ($kehadiranSemester['uts']['status'] === 'H') $totalHadir++;
                elseif ($kehadiranSemester['uts']['status'] === 'I') $totalIzin++;
                elseif ($kehadiranSemester['uts']['status'] === 'S') $totalSakit++;
                elseif ($kehadiranSemester['uts']['status'] === 'A') $totalAlpha++;
            }

            if ($kehadiranSemester['uas']['status'] !== '-') {
                $totalPertemuan++;
                if ($kehadiranSemester['uas']['status'] === 'H') $totalHadir++;
                elseif ($kehadiranSemester['uas']['status'] === 'I') $totalIzin++;
                elseif ($kehadiranSemester['uas']['status'] === 'S') $totalSakit++;
                elseif ($kehadiranSemester['uas']['status'] === 'A') $totalAlpha++;
            }

            $persentaseKehadiran = $totalPertemuan > 0 ? round(($totalHadir / $totalPertemuan) * 100, 1) : 0;

            return response()->json([
                'success' => true,
                'siswa' => [
                    'nama' => $siswa->user->name ?? 'No name',
                    'nis' => $siswa->nis,
                    'jurusan' => $siswa->jurusan->nama_jurusan ?? '-'
                ],
                'nilai' => $siswa->nilai->map(function($item) {
                    return [
                        'id' => $item->id,
                        'mapel' => $item->mapel ? [
                            'id' => $item->mapel->id,
                            'nama_mapel' => $item->mapel->nama_mapel
                        ] : null,
                        'nilai_tugas' => $item->nilai_tugas,
                        'nilai_uts' => $item->nilai_uts,
                        'nilai_uas' => $item->nilai_uas,
                        'nilai_praktikum' => $item->nilai_praktikum,
                        'rata_rata' => $item->rata_rata,
                        'grade' => $item->grade
                    ];
                }),
                'kehadiran' => $kehadiranSemester,
                'total_hadir' => $totalHadir,
                'total_izin' => $totalIzin,
                'total_sakit' => $totalSakit,
                'total_alpha' => $totalAlpha,
                'total_pertemuan' => $totalPertemuan,
                'persentase_kehadiran' => $persentaseKehadiran,
                'uts_status' => $kehadiranSemester['uts']['status'] ?? '-',
                'uas_status' => $kehadiranSemester['uas']['status'] ?? '-'
            ]);

        } catch (\Exception $e) {
            error_log('Error detail siswa: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan: ' . $e->getMessage()
            ], 404);
        }
    }

    public function unduhGuru(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'semester' => 'required|in:Ganjil,Genap',
            'tahun_ajaran' => 'required'
        ]);

        $kelasId = $request->kelas_id;
        $semester = $request->semester;
        $tahunAjaran = $request->tahun_ajaran;

        $kelas = Kelas::with('waliKelas.user')->findOrFail($kelasId);

        $guru = Guru::where('user_id', $user->id)->firstOrFail();

        // ===== MAPEL YANG DIAJAR GURU DI KELAS INI =====
        $mapelIds = DB::table('jadwal_kelas')
            ->where('guru_id', $guru->id)
            ->where('kelas_id', $kelasId)
            ->pluck('id_mata_pelajaran');

        $mapelNamaList = DB::table('jadwal_kelas')
            ->join('mata_pelajarans', 'jadwal_kelas.id_mata_pelajaran', '=', 'mata_pelajarans.id')
            ->where('jadwal_kelas.guru_id', $guru->id)
            ->where('jadwal_kelas.kelas_id', $kelasId)
            ->distinct()
            ->pluck('mata_pelajarans.nama_mapel');

        $mapel = $mapelNamaList->implode(', ');

        $siswaList = Siswa::where('kelas_id', $kelasId)
            ->join('users', 'siswa.user_id', '=', 'users.id')
            ->with([
                'user',
                'jurusan',
                'nilai' => function($q) use ($semester, $tahunAjaran, $mapelIds) {
                    $q->where('semester', $semester)
                    ->where('tahun_ajaran', $tahunAjaran)
                    ->when($mapelIds->isNotEmpty(), function ($qq) use ($mapelIds) {
                        $qq->whereIn('id_mata_pelajaran', $mapelIds);
                    })
                    ->with('mapel');
                }
            ])
            ->orderBy('users.name', 'asc')
            ->select('siswa.*')
            ->get();

        // ===== HITUNG RATA-RATA, GRADE, & KEHADIRAN UNTUK SETIAP SISWA =====
        foreach ($siswaList as $siswa) {
            $jumlahMapel = $siswa->nilai->count();

            if ($jumlahMapel > 0) {
                $rataRata        = $siswa->nilai->avg('rata_rata');
                $rataTugas       = $siswa->nilai->avg('nilai_tugas');
                $rataPraktikum   = $siswa->nilai->avg('nilai_praktikum');
                $rataUts         = $siswa->nilai->avg('nilai_uts');
                $rataUas         = $siswa->nilai->avg('nilai_uas');

                $siswa->rata_rata_nilai      = number_format($rataRata, 2);
                $siswa->grade                = $this->tentukanGrade($rataRata);
                $siswa->nilai_tugas_pdf      = $rataTugas !== null      ? number_format($rataTugas, 1)     : '-';
                $siswa->nilai_praktikum_pdf  = $rataPraktikum !== null  ? number_format($rataPraktikum, 1) : '-';
                $siswa->nilai_uts_pdf        = $rataUts !== null        ? number_format($rataUts, 1)       : '-';
                $siswa->nilai_uas_pdf        = $rataUas !== null        ? number_format($rataUas, 1)       : '-';
            } else {
                $siswa->rata_rata_nilai     = '-';
                $siswa->grade               = '-';
                $siswa->nilai_tugas_pdf     = '-';
                $siswa->nilai_praktikum_pdf = '-';
                $siswa->nilai_uts_pdf       = '-';
                $siswa->nilai_uas_pdf       = '-';
            }

            // Kehadiran
            $kehadiranSemester = $this->getKehadiranUntukSemester($siswa->id, $semester, $tahunAjaran);

            $totalHadir = 0; $totalIzin = 0; $totalSakit = 0; $totalAlpha = 0;
            $totalPertemuan = count($kehadiranSemester['pertemuan']);

            foreach ($kehadiranSemester['pertemuan'] as $pertemuan) {
                if ($pertemuan['status'] === 'H')      $totalHadir++;
                elseif ($pertemuan['status'] === 'I')  $totalIzin++;
                elseif ($pertemuan['status'] === 'S')  $totalSakit++;
                elseif ($pertemuan['status'] === 'A')  $totalAlpha++;
            }

            foreach (['uts', 'uas'] as $ujian) {
                if ($kehadiranSemester[$ujian]['status'] !== '-') {
                    $totalPertemuan++;
                    $st = $kehadiranSemester[$ujian]['status'];
                    if ($st === 'H')      $totalHadir++;
                    elseif ($st === 'I')  $totalIzin++;
                    elseif ($st === 'S')  $totalSakit++;
                    elseif ($st === 'A')  $totalAlpha++;
                }
            }

            $siswa->persentase_kehadiran = $totalPertemuan > 0
                ? round(($totalHadir / $totalPertemuan) * 100, 1) : 0;
            $siswa->total_hadir_pdf      = $totalHadir;
            $siswa->total_izin_pdf       = $totalIzin;
            $siswa->total_sakit_pdf      = $totalSakit;
            $siswa->total_alpha_pdf      = $totalAlpha;
            $siswa->total_pertemuan_pdf  = $totalPertemuan;
        }

        $pdf = Pdf::loadView('guru.pdf', compact(
            'kelas',
            'semester',
            'tahunAjaran',
            'siswaList',
            'guru',
            'mapel'
        ))->setPaper('A4', 'portrait');

        $filename = 'Laporan_Akademik_Kelas_' . $kelas->nama_kelas .
                    '_Semester_' . $semester .
                    '_TA_' . str_replace('/', '-', $tahunAjaran) . '.pdf';

        return $pdf->download($filename);
    }

    public function siswaIndex(Request $request)
    {
        $user = Auth::user();
        $profile = $user->siswaProfile;

        $siswa = Siswa::with(['kelas', 'jurusan'])
                    ->where('user_id', $user->id)
                    ->firstOrFail();

        // **PERBAIKAN: Ambil seluruh riwayat kelas dari tabel kelas_siswa**
        $kelasList = DB::table('kelas_siswa')
            ->join('kelas', 'kelas_siswa.kelas_id', '=', 'kelas.id')
            ->where('kelas_siswa.siswa_id', $profile->id)
            ->select('kelas.*', 'kelas_siswa.semester', 'kelas_siswa.tahun_ajaran')
            ->orderBy('kelas_siswa.tahun_ajaran', 'desc')
            ->orderBy('kelas_siswa.semester', 'desc')
            ->get();

        $kelasTerakhir = $kelasList->first();

        // **PERBAIKAN: Ambil seluruh semester unik dari kelas_siswa**
        $semesterList = $kelasList->pluck('semester')->unique()->values();

        $semesterAktif = $semesterList->last(); // atau logika sesuai kebutuhan

        // Default kelas = kelas terbaru
        $kelasId = $request->kelas_id ?? ($kelasList->first()->id ?? null);

        // Default semester = semester terbaru
        $semesterFilter = $request->semester ?? $semesterList->last();

        // Ambil tahun ajaran berdasarkan kelas yang dipilih
        $tahunAjaranFilter = null;
        if ($kelasId) {
            $kelasSelected = $kelasList->firstWhere('id', $kelasId);
            $tahunAjaranFilter = $kelasSelected->tahun_ajaran ?? null;
        }

        // **PERBAIKAN: Ambil laporan akademik sesuai nis + semester**
        $laporanakademik = LaporanAkademik::where('nis', $profile->nis)
            ->when($semesterFilter, function($query) use ($semesterFilter) {
                return $query->where('semester', $semesterFilter);
            })
            ->when($tahunAjaranFilter, function($query) use ($tahunAjaranFilter) {
                return $query->where('tahun_ajaran', $tahunAjaranFilter);
            })
            ->latest()
            ->first() ?? (object)[
                'catatan_akademik' => '-',
                'catatan_sikap'    => '-',
                'kesimpulan'       => '-',
                'rekomendasi'      => '-',
            ];

        // **PERBAIKAN: Ambil nilai sesuai nis + semester + tahun ajaran**
        $nilai = Nilai::where('nis', $profile->nis)
            ->when($semesterFilter, function($query) use ($semesterFilter) {
                return $query->where('semester', $semesterFilter);
            })
            ->when($tahunAjaranFilter, function($query) use ($tahunAjaranFilter) {
                return $query->where('tahun_ajaran', $tahunAjaranFilter);
            })
            ->with('mapel')
            ->get();

        // **PERBAIKAN: Ambil kehadiran sesuai siswa + semester + tahun ajaran**
        $kehadiranQuery = Kehadiran::where('siswa_id', $profile->id)
            ->when($semesterFilter, function($query) use ($semesterFilter) {
                return $query->where('semester', $semesterFilter);
            })
            ->when($tahunAjaranFilter, function($query) use ($tahunAjaranFilter) {
                return $query->where('tahun_ajaran', $tahunAjaranFilter);
            });

        $kehadiranData = $kehadiranQuery->get();

        $kehadiran = (object)[
            'hadir' => $kehadiranData->where('status', 'hadir')->count(),
            'izin'  => $kehadiranData->where('status', 'izin')->count(),
            'sakit' => $kehadiranData->where('status', 'sakit')->count(),
            'alpha' => $kehadiranData->where('status', 'alpa')->count(),
        ];

        // **PERBAIKAN: Ambil wali kelas dari kelas terakhir**
        $waliKelas = '-';
        if ($kelasTerakhir) {
            $kelasDetail = Kelas::with('waliKelas.user')->find($kelasTerakhir->id);
            $waliKelas = $kelasDetail->waliKelas->user->name ?? '-';
        }

        return view('Siswa.laporanakademik', compact(
            'user',
            'siswa',
            'profile',
            'kelasList',
            'kelasTerakhir',
            'kelasId',
            'semesterList',
            'semesterFilter',
            'semesterAktif',
            'nilai',
            'waliKelas',
            'kehadiran',
            'laporanakademik'
        ));
    }

    public function unduh($nis, $semester = null)
    {
        $user = Auth::user();
        $profile = $user->siswaProfile;

        if (!$semester) {
            $semester = request('semester');
        }

        if (!$semester) {
            return back()->with('error', 'Semester harus dipilih.');
        }

        // Ambil data siswa dengan relasi lengkap
        $siswa = Siswa::with(['user', 'jurusan'])
            ->where('nis', $nis)
            ->firstOrFail();

        // Validasi akses (hanya siswa sendiri)
        if (Auth::user()->role === 'siswa' && Auth::user()->id !== $siswa->user_id) {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengunduh laporan ini.');
        }

        // Ambil kelas siswa berdasarkan semester
        $kelasSiswa = DB::table('kelas_siswa')
            ->where('siswa_id', $siswa->id)
            ->where('semester', $semester)
            ->orderBy('tahun_ajaran', 'desc')
            ->first();

        if (!$kelasSiswa) {
            return back()->with('error', 'Data kelas untuk semester ini tidak ditemukan.');
        }

        // Ambil kelas beserta wali kelas (dengan eager loading)
        $kelas = Kelas::with('waliKelas.user')->find($kelasSiswa->kelas_id);

        $tahunAjaran = $kelasSiswa->tahun_ajaran;
        $kelasAktif  = $kelas;
        $waliKelas   = $kelas->waliKelas->user->name ?? '-';
        $nip         = $kelas->waliKelas->nip ?? '-';    // ✅ NIP wali kelas

        // Laporan akademik
        $laporanakademik = LaporanAkademik::where('nis', $nis)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->first() ?? (object)[
                'catatan_akademik' => '-',
                'catatan_sikap'    => '-',
                'kesimpulan'       => '-',
                'rekomendasi'      => '-',
            ];

        // Nilai
        $nilai = Nilai::where('nis', $nis)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->with('mapel')
            ->orderBy('id_mata_pelajaran', 'asc')
            ->get();

        // Kehadiran
        $absensi = (object)[
            'hadir' => Kehadiran::where('siswa_id', $siswa->id)
                ->where('semester', $semester)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('status', 'hadir')->count(),
            'izin'  => Kehadiran::where('siswa_id', $siswa->id)
                ->where('semester', $semester)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('status', 'izin')->count(),
            'sakit' => Kehadiran::where('siswa_id', $siswa->id)
                ->where('semester', $semester)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('status', 'sakit')->count(),
            'alpha' => Kehadiran::where('siswa_id', $siswa->id)
                ->where('semester', $semester)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('status', 'alpa')->count(),
        ];

        // Gunakan view yang sesuai (misal 'siswa.pdf')
        $pdf = Pdf::loadView('siswa.pdf', compact(
            'siswa',
            'kelasAktif',
            'laporanakademik',
            'nilai',
            'absensi',
            'waliKelas',
            'nip',              // ✅ kirim variabel nip
            'semester',
            'tahunAjaran'
        ))->setPaper('A4', 'portrait');

        $filename = 'Laporan_Akademik_' .
            str_replace(' ', '_', $siswa->user->name) .
            '_Semester_' . $semester .
            '_TA_' . str_replace('/', '-', $tahunAjaran) .
            '.pdf';

        return $pdf->download($filename);
    }
}
