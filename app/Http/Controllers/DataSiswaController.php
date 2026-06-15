<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\JadwalKelas;
use App\Models\Nilai;
use App\Models\Kehadiran;

class DataSiswaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $guru = Guru::where('user_id', $user->id)->firstOrFail();

        // Tahun ajaran aktif
        $tahunAjaranAktif = $this->getTahunAjaranAktif();
        $tahunAjaran = $request->input('tahun_ajaran', $tahunAjaranAktif);

        // Semester aktif
        $semesterAktif = $this->getSemesterAktif();
        $semester = $request->input('semester', $semesterAktif);

        // Ambil kelas dari JadwalKelas yang diampu guru
        $jadwalKelasList = JadwalKelas::where('guru_id', $guru->id)
            ->distinct()
            ->pluck('kelas_id')
            ->toArray();

        // Query kelas yang diampu
        $kelasList = Kelas::whereIn('id', $jadwalKelasList)
            ->where('tahun_ajaran', $tahunAjaran)
            ->with([
                'waliKelas.user',
                'siswa.user'
            ])
            ->withCount('siswa')
            ->orderBy('nama_kelas')
            ->get();

        // Load jadwal kelas for each kelas
        foreach ($kelasList as $kelas) {
            $kelas->jadwal_kelas_guru = JadwalKelas::where('kelas_id', $kelas->id)
                ->where('guru_id', $guru->id)
                ->with('mapel')
                ->get();
        }

        // Hitung total
        $totalKelas = $kelasList->count();
        $totalSiswa = $kelasList->sum('siswa_count');

        // List tahun ajaran untuk dropdown
        $listTahunAjaran = Kelas::select('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran', 'desc')
            ->pluck('tahun_ajaran');

        // Ambil semua mapel yang diampu guru untuk filter
        $mapelGuru = JadwalKelas::where('guru_id', $guru->id)
            ->with('mapel')
            ->get()
            ->pluck('mapel.nama_mapel', 'mapel.id')
            ->unique()
            ->filter();

        return view('Guru.datasiswa', compact(
            'guru',
            'kelasList',
            'totalKelas',
            'totalSiswa',
            'tahunAjaranAktif',
            'tahunAjaran',
            'listTahunAjaran',
            'semesterAktif',
            'semester',
            'mapelGuru'
        ));
    }

    public function showKelas($kelasId)
    {
        $user = Auth::user();
        $guru = Guru::where('user_id', $user->id)->firstOrFail();

        // Ambil kelas beserta siswa
        $kelas = Kelas::with([
            'waliKelas.user',
            'siswa' => function($query) {
                $query->with(['user', 'jurusan'])
                    ->orderBy('nis');
            }
        ])->findOrFail($kelasId);

        // Verifikasi bahwa guru mengampu kelas ini
        $isTeaching = JadwalKelas::where('guru_id', $guru->id)
            ->where('kelas_id', $kelasId)
            ->exists();

        if (!$isTeaching) {
            abort(403, 'Anda tidak mengampu kelas ini');
        }

        return view('guru.kelas-detail', compact('guru', 'kelas'));
    }

    private function getTahunAjaranAktif()
    {
        // Jika bulan >= 7 (Juli-Desember) maka tahun ajaran Tahun/Tahun+1
        // Jika bulan <= 6 (Januari-Juni) maka tahun ajaran Tahun-1/Tahun
        if (date('n') >= 7) {
            return date('Y') . '/' . (date('Y') + 1);
        } else {
            return (date('Y') - 1) . '/' . date('Y');
        }
    }

    private function getSemesterAktif()
    {
        // Bulan 7-12 = Semester Ganjil
        // Bulan 1-6 = Semester Genap
        return (date('n') >= 7 && date('n') <= 12) ? 'Ganjil' : 'Genap';
    }

    public function getSiswaByKelas($kelasId)
    {
        try {
            $user = Auth::user();
            $guru = Guru::where('user_id', $user->id)->firstOrFail();

            // Verifikasi bahwa guru mengampu kelas ini
            $isTeaching = JadwalKelas::where('guru_id', $guru->id)
                ->where('kelas_id', $kelasId)
                ->exists();

            if (!$isTeaching) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak mengampu kelas ini'
                ], 403);
            }

            // Ambil data siswa
            $kelas = Kelas::with(['siswa.user'])->findOrFail($kelasId);

            $siswaData = $kelas->siswa->map(function($siswa) {
                return [
                    'id' => $siswa->id,
                    'nis' => $siswa->nis,
                    'nama' => $siswa->user->name ?? 'N/A',
                    'jenis_kelamin' => $siswa->jenis_kelamin ?? '-',
                    'kontak' => $siswa->no_telepon ?? $siswa->user->email ?? '-',
                    'status' => $siswa->status ?? 'Aktif'
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $siswaData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
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

    public function kelolaNilai(Request $request)
    {
        $user = Auth::user();
        $guru = Guru::where('user_id', $user->id)->firstOrFail();

        $mapelIds = JadwalKelas::where('guru_id', $guru->id)
            ->pluck('id_mata_pelajaran')
            ->unique();

        // Then fetch the actual MataPelajaran records
        $mataPelajaranGuru = MataPelajaran::whereIn('id', $mapelIds)->get();

        // Filter
        $kelasId = $request->input('kelas_id');
        $semester = $request->input('semester', 'Ganjil');
        $mapelId = $request->input('mapel_id');

        // TAMBAHKAN TAHUN AJARAN
        $tahunAjaranAktif = (now()->month >= 7)
            ? now()->year . '/' . (now()->year + 1)
            : (now()->year - 1) . '/' . now()->year;
        $tahunAjaran = $request->input('tahun_ajaran', $tahunAjaranAktif);

        // List untuk dropdown
        $listKelas = Kelas::where('tahun_ajaran', $tahunAjaran)
            ->orderBy('nama_kelas')
            ->get();
        $listMapel = $mataPelajaranGuru;

        // TAMBAHKAN LIST TAHUN AJARAN
        $listTahunAjaran = Kelas::select('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran', 'desc')
            ->pluck('tahun_ajaran');

        $siswaData = collect();
        $infoCard = []; // TAMBAHKAN INFO CARD

        if ($kelasId && $mapelId) {
            // Cari siswa melalui tabel kelas_siswa dengan filter tahun ajaran dan semester
            $siswaIds = DB::table('kelas_siswa')
                ->where('kelas_id', $kelasId)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->pluck('siswa_id');

            $siswaList = Siswa::whereIn('id', $siswaIds)
                ->with('user')
                ->orderBy('nis')
                ->get();

            $totalSudahDinilai = 0;
            $totalBelumDinilai = 0;
            $rataRataKelas = 0;
            $totalNilai = 0;

            foreach ($siswaList as $siswa) {
                // Cek apakah sudah ada nilai
                $nilai = Nilai::where('nis', $siswa->nis)
                    ->where('nip', $guru->nip)
                    ->where('id_mata_pelajaran', $mapelId)
                    ->where('semester', $semester)
                    ->first();

                $nilaiExists = $nilai && ($nilai->nilai_tugas || $nilai->nilai_praktikum || $nilai->nilai_uts || $nilai->nilai_uas);

                if ($nilaiExists) {
                    $totalSudahDinilai++;
                    $totalNilai += $nilai->rata_rata;
                } else {
                    $totalBelumDinilai++;
                }

                $siswaData->push([
                    'id' => $siswa->id,
                    'nis' => $siswa->nis,
                    'nama' => $siswa->user->name ?? 'N/A',
                    'nilai_id' => $nilai ? $nilai->id : null,
                    'nilai_tugas' => $nilai ? ($nilai->nilai_tugas ?? '-') : '-',
                    'nilai_praktikum' => $nilai ? ($nilai->nilai_praktikum ?? '-') : '-',
                    'nilai_uts' => $nilai ? ($nilai->nilai_uts ?? '-') : '-',
                    'nilai_uas' => $nilai ? ($nilai->nilai_uas ?? '-') : '-',
                    'sikap' => $nilai ? ($nilai->sikap ?? '-') : '-',
                    'rata_rata' => $nilai ? $nilai->rata_rata : 0,
                    'grade' => $nilai ? $nilai->grade : '-',
                    'status_nilai' => $nilaiExists ? 'Sudah' : 'Belum' // TAMBAHKAN STATUS
                ]);
            }

            // Hitung info card
            if ($totalSudahDinilai > 0) {
                $rataRataKelas = round($totalNilai / $totalSudahDinilai, 2);
            }

            $persentaseDinilai = $siswaList->count() > 0 ?
                round(($totalSudahDinilai / $siswaList->count()) * 100) : 0;

            // TAMBAHKAN DATA INFO CARD
            $infoCard = [
                'total_siswa' => $siswaList->count(),
                'sudah_dinilai' => $totalSudahDinilai,
                'belum_dinilai' => $totalBelumDinilai,
                'persentase_dinilai' => $persentaseDinilai,
                'rata_rata_kelas' => $rataRataKelas,
                'kelas_nama' => Kelas::find($kelasId)->nama_kelas ?? '-',
                'mapel_nama' => MataPelajaran::find($mapelId)->nama_mapel ?? '-'
            ];
        }

        return view('Guru.keloladata.nilai', compact(
            'guru',
            'listKelas',
            'listMapel',
            'listTahunAjaran', // TAMBAHKAN
            'kelasId',
            'semester',
            'mapelId',
            'tahunAjaran', // TAMBAHKAN
            'siswaData',
            'infoCard' // TAMBAHKAN
        ));
    }

    public function getNilaiSiswa(Request $request)
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

            $nis = $request->input('nis');
            $semester = $request->input('semester');
            $tahunAjaran = $request->input('tahun_ajaran');
            $nip = $request->input('nip', $guru->nip);

            // Cari semua nilai untuk siswa ini di semester dan tahun ajaran tertentu
            // PERHATIAN: Karena mungkin ada beberapa mata pelajaran, kita ambil yang pertama
            $nilai = Nilai::where('nis', $nis)
                ->where('nip', $nip)
                ->where('semester', $semester)
                ->where('tahun_ajaran', $tahunAjaran)
                ->first();

            // Jika tidak ditemukan dengan nip guru, coba cari nilai apa saja untuk siswa ini
            if (!$nilai) {
                $nilai = Nilai::where('nis', $nis)
                    ->where('semester', $semester)
                    ->where('tahun_ajaran', $tahunAjaran)
                    ->first();
            }

            // Ambil mata pelajaran jika ada
            $mataPelajaran = null;
            if ($nilai && $nilai->id_mata_pelajaran) {
                $mataPelajaran = MataPelajaran::find($nilai->id_mata_pelajaran);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'nilai' => $nilai,
                    'mata_pelajaran' => $mataPelajaran
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeOrUpdateNilai(Request $request)
    {
        $request->validate([
            'nis' => 'required|exists:siswa,nis',
            'nip' => 'required|exists:guru,nip',
            'id_mata_pelajaran' => 'required|exists:mata_pelajarans,id',
            'semester' => 'required|string',
            'tahun_ajaran' => 'required|string',
            'nilai_tugas' => 'nullable|integer|min:0|max:100',
            'nilai_praktikum' => 'nullable|integer|min:0|max:100',
            'nilai_uts' => 'nullable|integer|min:0|max:100',
            'nilai_uas' => 'nullable|integer|min:0|max:100',
            'sikap' => 'required|in:A,B,C,D,E'
        ]);

        // Hitung rata-rata
        $tugas = $request->nilai_tugas ?? 0;
        $praktikum = $request->nilai_praktikum ?? 0;
        $uts = $request->nilai_uts ?? 0;
        $uas = $request->nilai_uas ?? 0;

        $rataRata = ($tugas + $praktikum + $uts + $uas) / 4;

        // Tentukan grade
        $grade = $this->hitungGrade($rataRata);

        // Update or Create
        Nilai::updateOrCreate(
            [
                'nis' => $request->nis,
                'nip' => $request->nip,
                'id_mata_pelajaran' => $request->id_mata_pelajaran,
                'semester' => $request->semester,
                'tahun_ajaran' => $request->tahun_ajaran
            ],
            [
                'nilai_tugas' => $request->nilai_tugas,
                'nilai_praktikum' => $request->nilai_praktikum,
                'nilai_uts' => $request->nilai_uts,
                'nilai_uas' => $request->nilai_uas,
                'sikap' => $request->sikap,
                'rata_rata' => $rataRata,
                'grade' => $grade,
                'tahun_ajaran' => $request->tahun_ajaran
            ]
        );

        return back()->with('success', 'Nilai berhasil disimpan!');
    }

    private function hitungGrade($nilai)
    {
        if ($nilai >= 85) return 'A';
        if ($nilai >= 75) return 'B';
        if ($nilai >= 60) return 'C';
        if ($nilai >= 50) return 'D';
        return 'E';
    }

    // ==================== KELOLA KEHADIRAN ====================

    public function kelolaKehadiran(Request $request)
    {
        $user = Auth::user();
        $guru = Guru::where('user_id', $user->id)->firstOrFail();

        // TAMBAHKAN TAHUN AJARAN
        $tahunAjaranAktif = (now()->month >= 7)
            ? now()->year . '/' . (now()->year + 1)
            : (now()->year - 1) . '/' . now()->year;
        $tahunAjaran = $request->input('tahun_ajaran', $tahunAjaranAktif);

        // Filter
        $kelasId = $request->input('kelas_id');
        $semester = $request->input('semester', 'Ganjil');
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        // List untuk dropdown
        $listKelas = Kelas::where('tahun_ajaran', $tahunAjaran)
            ->orderBy('nama_kelas')
            ->get();

        // TAMBAHKAN LIST TAHUN AJARAN
        $listTahunAjaran = Kelas::select('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran', 'desc')
            ->pluck('tahun_ajaran');

        $siswaData = collect();
        $infoCard = []; // TAMBAHKAN INFO CARD

        if ($kelasId) {
            // Cari siswa melalui tabel kelas_siswa dengan filter tahun ajaran dan semester
            $siswaIds = DB::table('kelas_siswa')
                ->where('kelas_id', $kelasId)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->pluck('siswa_id');

            $siswaList = Siswa::whereIn('id', $siswaIds)
                ->with('user')
                ->orderBy('nis')
                ->get();

            // Hitung statistik untuk info card
            $totalHadirHariIni = 0;
            $totalIzinHariIni = 0;
            $totalSakitHariIni = 0;
            $totalAlpaHariIni = 0;
            $totalSiswa = $siswaList->count();

            foreach ($siswaList as $siswa) {
                // Cek kehadiran pada tanggal tersebut
                $kehadiran = Kehadiran::where('siswa_id', $siswa->id)
                    ->where('tanggal', $tanggal)
                    ->first();

                // Hitung statistik
                if ($kehadiran) {
                    switch ($kehadiran->status) {
                        case 'hadir': $totalHadirHariIni++; break;
                        case 'izin': $totalIzinHariIni++; break;
                        case 'sakit': $totalSakitHariIni++; break;
                        case 'alpa': $totalAlpaHariIni++; break;
                    }
                } else {
                    // Default ke hadir jika belum ada data
                    $totalHadirHariIni++;
                }

                $siswaData->push([
                    'id' => $siswa->id,
                    'nis' => $siswa->nis,
                    'nama' => $siswa->user->name ?? 'N/A',
                    'kehadiran_id' => $kehadiran->id ?? null,
                    'status' => $kehadiran->status ?? 'Belum Diisi',
                    'keterangan' => $kehadiran->keterangan ?? ''
                ]);
            }

            // TAMBAHKAN DATA INFO CARD
            $persentaseHadir = $totalSiswa > 0 ? round(($totalHadirHariIni / $totalSiswa) * 100) : 0;

            $infoCard = [
                'total_siswa' => $totalSiswa,
                'hadir' => $totalHadirHariIni,
                'izin' => $totalIzinHariIni,
                'sakit' => $totalSakitHariIni,
                'alpa' => $totalAlpaHariIni,
                'persentase_hadir' => $persentaseHadir,
                'kelas_nama' => Kelas::find($kelasId)->nama_kelas ?? '-',
                'tanggal_formatted' => \Carbon\Carbon::parse($tanggal)->format('d F Y'),
                'hari' => \Carbon\Carbon::parse($tanggal)->translatedFormat('l')
            ];
        }

        return view('Guru.keloladata.kehadiran', compact(
            'guru',
            'listKelas',
            'listTahunAjaran', // TAMBAHKAN
            'kelasId',
            'semester',
            'tahunAjaran', // TAMBAHKAN
            'tanggal',
            'siswaData',
            'infoCard' // TAMBAHKAN
        ));
    }

    public function storeKehadiranHarian(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kehadiran' => 'required|array',
            'kehadiran.*.siswa_id' => 'required|exists:siswa,id',
            'kehadiran.*.status' => 'required|in:hadir,izin,sakit,alpa',
            'tahun_ajaran' => 'required|string', // TAMBAHKAN VALIDASI
            'semester' => 'required|string' // TAMBAHKAN JIKA PERLU
        ]);

        foreach ($request->kehadiran as $data) {
            Kehadiran::updateOrCreate(
                [
                    'siswa_id' => $data['siswa_id'],
                    'tanggal' => $request->tanggal
                ],
                [
                    'status' => $data['status'],
                    'keterangan' => $data['keterangan'] ?? null,
                    'tahun_ajaran' => $request->tahun_ajaran, // TAMBAHKAN INI
                    'semester' => $request->semester // TAMBAHKAN JIKA TABEL MEMILIKI FIELD INI
                ]
            );
        }

        return back()->with('success', 'Data kehadiran berhasil disimpan!');
    }

}
