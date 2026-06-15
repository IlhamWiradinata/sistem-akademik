<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\Kehadiran;
use App\Models\Nilai;
use App\Models\MataPelajaran;
use App\Models\Guru;
use Carbon\Carbon;

class MonitoringController extends Controller
{
    // ==================== MONITORING KEHADIRAN ====================
    public function monitoringKehadiran(Request $request)
    {
        if ($request->ajax() && $request->has('tahun_ajaran') && !$request->has('semester')) {
            return Kelas::where('tahun_ajaran', $request->tahun_ajaran)
                ->orderBy('nama_kelas')
                ->get(['id', 'nama_kelas']);
        }

        $tahunAjaran = $request->tahun_ajaran;
        $kelas       = $request->kelas_id;
        $semester    = $request->input('semester', 'Ganjil');
        $bulan       = $request->input('bulan'); // biarkan null jika tidak dipilih
        $jurusan     = $request->input('jurusan');
        $filterKehadiran = $request->input('filter_kehadiran');

        // List Tahun Ajaran
        $listTahunAjaran = Kelas::select('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran', 'desc')
            ->pluck('tahun_ajaran');

        // List jurusan dan kelas
        $listJurusan = Jurusan::pluck('nama_jurusan')->toArray();
        $listKelas = collect();
        if ($tahunAjaran) {
            $listKelas = Kelas::where('tahun_ajaran', $tahunAjaran)
                ->orderBy('nama_kelas')
                ->get();
        }

        $siswaData = collect();
        $hariEfektif = 0;

        if ($kelas && $tahunAjaran && $semester) {
            // Ambil siswa ID dari pivot kelas_siswa
            $siswaIds = DB::table('kelas_siswa')
                ->where('kelas_id', $kelas)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->pluck('siswa_id');

            $siswas = Siswa::with('user')
                ->whereIn('id', $siswaIds)
                ->get();

            // Tentukan range tanggal semester
            [$awal, $akhir] = explode('/', $tahunAjaran);
            if ($semester === 'Ganjil') {
                $start = $awal . '-07-01';
                $end   = $awal . '-12-31';
            } else {
                $start = $akhir . '-01-01';
                $end   = $akhir . '-06-30';
            }

            // Hitung hari efektif (Senin-Jumat) untuk periode yang difilter
            $hariEfektif = $this->hitungHariEfektif($start, $end, $bulan);

            foreach ($siswas as $siswa) {
                $query = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereBetween('tanggal', [$start, $end]);

                // Filter bulan jika ada
                if ($bulan) {
                    $query->whereMonth('tanggal', $bulan);
                }

                $kehadiran = $query->get();

                $hadir = $kehadiran->where('status', 'hadir')->count();
                $izin  = $kehadiran->where('status', 'izin')->count();
                $sakit = $kehadiran->where('status', 'sakit')->count();
                $alpa  = $kehadiran->where('status', 'alpa')->count();

                $total = $hadir + $izin + $sakit + $alpa;
                $rasio = $total > 0 ? round(($hadir / $total) * 100, 2) : 0;

                $siswaData->push([
                    'id'    => $siswa->id,
                    'nis'   => $siswa->nis,
                    'nama'  => $siswa->user->name,
                    'hadir' => $hadir,
                    'izin'  => $izin,
                    'sakit' => $sakit,
                    'alpa'  => $alpa,
                    'rasio' => $rasio,
                    'total' => $total
                ]);
            }

            // Filter berdasarkan persentase kehadiran
            if ($filterKehadiran) {
                switch ($filterKehadiran) {
                    case 'diatas_90':
                        $siswaData = $siswaData->filter(fn($s) => $s['rasio'] >= 90);
                        break;
                    case 'dibawah_75':
                        $siswaData = $siswaData->filter(fn($s) => $s['rasio'] < 75);
                        break;
                    case 'perlu_perhatian':
                        $siswaData = $siswaData->filter(fn($s) => $s['rasio'] < 60 || $s['alpa'] > 3);
                        break;
                }
            }
        }

        // Daftar bulan untuk dropdown (agar view bisa menampilkan nama bulan)
        $bulanList = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        return view('Admin.monitoring.kehadiran', compact(
            'siswaData',
            'tahunAjaran',
            'kelas',
            'bulan',
            'semester',
            'jurusan',
            'listJurusan',
            'listTahunAjaran',
            'listKelas',
            'bulanList',
            'hariEfektif'
        ));
    }

    private function hitungHariEfektif($start, $end, $bulan = null)
    {
        $startDate = \Carbon\Carbon::parse($start);
        $endDate = \Carbon\Carbon::parse($end);

        if ($bulan && is_numeric($bulan)) {
            $bulanInt = (int) $bulan;
            $tahun = $startDate->year;

            // Buat tanggal awal dan akhir bulan yang dipilih (menggunakan tahun semester)
            $bulanStart = \Carbon\Carbon::create($tahun, $bulanInt, 1, 0, 0, 0);
            $bulanEnd = $bulanStart->copy()->endOfMonth();

            // Sesuaikan dengan batasan semester
            if ($bulanStart->lt($startDate)) {
                $startDate = $startDate->copy();
            } else {
                $startDate = $bulanStart->copy();
            }

            if ($bulanEnd->gt($endDate)) {
                $endDate = $endDate->copy();
            } else {
                $endDate = $bulanEnd->copy();
            }

            // Jika rentang tidak valid (misal bulan di luar semester), langsung return 0
            if ($startDate->gt($endDate)) {
                return 0;
            }
        }

        $hariEfektif = 0;
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            // Hari Senin = 1, Selasa = 2, ..., Jumat = 5
            if ($current->dayOfWeek >= 1 && $current->dayOfWeek <= 5) {
                $hariEfektif++;
            }
            $current->addDay();
        }

        return $hariEfektif;
    }

    public function detailKehadiran(Request $request, $siswaId)
    {
        $siswa = Siswa::with('user', 'kelas', 'jurusan')->findOrFail($siswaId);

        $query = Kehadiran::where('siswa_id', $siswaId);

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('keterangan', 'like', '%' . $request->search . '%');
        }

        $kehadiran = $query->orderBy('tanggal', 'desc')
                        ->paginate(20)
                        ->appends($request->query());

        return view('Admin.monitoring.detailkehadiran', compact('siswa', 'kehadiran'));
    }

    public function storeKehadiran(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,izin,sakit,alpa',
            'keterangan' => 'nullable|string',
            'semester'     => 'required|string',
            'tahun_ajaran' => 'required|string'
        ]);

        // Cek apakah sudah ada kehadiran di tanggal tersebut
        $exists = Kehadiran::where('siswa_id', $request->siswa_id)
            ->where('tanggal', $request->tanggal)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Data kehadiran untuk tanggal ini sudah ada!');
        }

        Kehadiran::create([
        'siswa_id'     => $request->siswa_id,
        'tanggal'      => $request->tanggal,
        'status'       => $request->status,
        'keterangan'   => $request->keterangan,
        'semester'     => $request->semester,
        'tahun_ajaran' => $request->tahun_ajaran,
        ]);

        return back()->with('success', 'Data kehadiran berhasil ditambahkan!');
    }

    public function updateKehadiran(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,izin,sakit,alpa',
            'keterangan' => 'nullable|string'
        ]);

        $kehadiran = Kehadiran::findOrFail($id);
        $kehadiran->update($request->all());

        return back()->with('success', 'Data kehadiran berhasil diupdate!');
    }

    public function deleteKehadiran($id)
    {
        $kehadiran = Kehadiran::findOrFail($id);
        $kehadiran->delete();

        return back()->with('success', 'Data kehadiran berhasil dihapus!');
    }

    // ==================== MONITORING NILAI ====================

    public function monitoringNilai(Request $request)
    {
        $tahunAjaranAktif = (now()->month >= 7)
            ? now()->year . '/' . (now()->year + 1)
            : (now()->year - 1) . '/' . now()->year;
        $tahunAjaran = $request->tahun_ajaran ?? $tahunAjaranAktif;
        $listTahunAjaran = Kelas::select('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran', 'desc')
            ->pluck('tahun_ajaran');

        $grade = $request->input('grade');

        $kelas  = $request->kelas_id;
        $semester = $request->input('semester', 'Ganjil');
        $mapel = $request->input('mapel');

        // Inisialisasi $siswaData
        $siswaData = collect();

        // Ambil list untuk filter
        $listKelas = Kelas::where('tahun_ajaran', $tahunAjaran)
            ->orderBy('nama_kelas')
            ->get();

        $listMapel = MataPelajaran::pluck('nama_mapel', 'id')->toArray();

        // Jika kelas dan tahun ajaran dipilih
        if ($kelas && $tahunAjaran) {
            // Ambil siswa ID dari pivot kelas_siswa (SAMA seperti monitoring kehadiran)
            $siswaIds = DB::table('kelas_siswa')
                ->where('kelas_id', $kelas)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->pluck('siswa_id');

            // Ambil data siswa
            $siswaList = Siswa::with('user')
                ->whereIn('id', $siswaIds)
                ->get();

            foreach ($siswaList as $siswa) {
                $nilaiQuery = Nilai::where('nis', $siswa->nis)
                    ->where('semester', $semester);

                if ($mapel) {
                    $nilaiQuery->where('id_mata_pelajaran', $mapel);
                }

                $nilaiData = $nilaiQuery->get();
                $rataRata = $nilaiData->count() > 0 ? round($nilaiData->avg('rata_rata'), 2) : 0;

                // Tampilkan semua siswa, meskipun belum ada nilai
                $siswaData->push([
                    'id' => $siswa->id,
                    'nis' => $siswa->nis,
                    'nama' => $siswa->user->name ?? $siswa->nama_lengkap, // fallback
                    'total_mapel' => $nilaiData->count(),
                    'rata_rata' => $nilaiData->count() > 0 ? round($nilaiData->avg('rata_rata'), 2) : 0,
                    'nilai_tertinggi' => $nilaiData->count() > 0 ? number_format($nilaiData->max('rata_rata'), 2) : '-',
                    'nilai_terendah' => $nilaiData->count() > 0 ? number_format($nilaiData->min('rata_rata'), 2) : '-',
                ]);
            }
        }

        return view('Admin.monitoring.nilai', compact(
            'siswaData',
            'semester',
            'mapel',
            'tahunAjaran',
            'listTahunAjaran',
            'listKelas',
            'listMapel',
            'kelas'
        ));
    }

    public function detailNilai($siswaId)
    {
        $siswa = Siswa::with('user', 'kelas', 'jurusan')->findOrFail($siswaId);
        $nilai = Nilai::where('nis', $siswa->nis)
            ->with('mapel', 'guru')
            ->orderBy('semester', 'desc')
            ->get();

        return view('Admin.monitoring.detailnilai', compact('siswa', 'nilai'));
    }

    public function storeNilai(Request $request)
    {
        $request->validate([
            'nis' => 'required|exists:siswa,nis',
            'nip' => 'required|exists:guru,nip',
            'id_mata_pelajaran' => 'required|exists:mata_pelajarans,id',
            'nilai_tugas' => 'nullable|integer|min:0|max:100',
            'nilai_praktikum' => 'nullable|integer|min:0|max:100',
            'nilai_uts' => 'nullable|integer|min:0|max:100',
            'nilai_uas' => 'nullable|integer|min:0|max:100',
            'sikap' => 'nullable|string|max:2',
            'semester' => 'required|string',
            'tahun_ajaran' => 'required|string' // TAMBAHKAN INI
        ]);

        // Hitung rata-rata
        $tugas = $request->nilai_tugas ?? 0;
        $praktikum = $request->nilai_praktikum ?? 0;
        $uts = $request->nilai_uts ?? 0;
        $uas = $request->nilai_uas ?? 0;

        $rataRata = ($tugas + $praktikum + $uts + $uas) / 4;

        // Tentukan grade
        $grade = $this->hitungGrade($rataRata);

        // Cek duplikasi
        $exists = Nilai::where('nis', $request->nis)
            ->where('id_mata_pelajaran', $request->id_mata_pelajaran)
            ->where('semester', $request->semester)
            ->where('tahun_ajaran', $request->tahun_ajaran) // TAMBAHKAN FILTER TAHUN AJARAN
            ->exists();

        if ($exists) {
            return back()->with('error', 'Nilai untuk mata pelajaran, semester, dan tahun ajaran ini sudah ada!');
        }

        Nilai::create([
            'nis' => $request->nis,
            'nip' => $request->nip,
            'id_mata_pelajaran' => $request->id_mata_pelajaran,
            'nilai_tugas' => $request->nilai_tugas,
            'nilai_praktikum' => $request->nilai_praktikum,
            'nilai_uts' => $request->nilai_uts,
            'nilai_uas' => $request->nilai_uas,
            'sikap' => $request->sikap,
            'grade' => $grade,
            'rata_rata' => $rataRata,
            'semester' => $request->semester,
            'tahun_ajaran' => $request->tahun_ajaran,
            'updated_at' => now(),
            'created_at' => now()
        ]);

        return back()->with('success', 'Nilai berhasil ditambahkan!');
    }

    public function updateNilai(Request $request, $id)
    {
        $request->validate([
            'nilai_tugas' => 'nullable|integer|min:0|max:100',
            'nilai_praktikum' => 'nullable|integer|min:0|max:100',
            'nilai_uts' => 'nullable|integer|min:0|max:100',
            'nilai_uas' => 'nullable|integer|min:0|max:100',
            'sikap' => 'nullable|in:A,B,C,D,E',
        ]);

        $nilai = Nilai::findOrFail($id);

        // Hitung rata-rata
        $tugas = $request->nilai_tugas ?? 0;
        $praktikum = $request->nilai_praktikum ?? 0;
        $uts = $request->nilai_uts ?? 0;
        $uas = $request->nilai_uas ?? 0;

        $rataRata = ($tugas + $praktikum + $uts + $uas) / 4;
        $grade = $this->hitungGrade($rataRata);

        $nilai->update([
            'nilai_tugas' => $request->nilai_tugas,
            'nilai_praktikum' => $request->nilai_praktikum,
            'nilai_uts' => $request->nilai_uts,
            'nilai_uas' => $request->nilai_uas,
            'sikap' => $request->sikap,
            'grade' => $grade,
            'rata_rata' => $rataRata
        ]);

         return redirect()->back()->with('success', 'Nilai berhasil diupdate');
    }

    public function deleteNilai($id)
    {
        $nilai = Nilai::findOrFail($id);
        $nilai->delete();

        return back()->with('success', 'Nilai berhasil dihapus!');
    }

    // Helper function untuk hitung grade
    private function hitungGrade($nilai)
    {
        if ($nilai >= 85) return 'A';
        if ($nilai >= 75) return 'B';
        if ($nilai >= 60) return 'C';
        if ($nilai >= 50) return 'D';
        return 'E';
    }

    // ==================== INPUT KEHADIRAN BATCH ====================

    public function inputKehadiranBatch(Request $request)
    {
        $kelas = $request->input('kelas_id');
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        $listKelas = Kelas::all();
        $siswas = collect();

        if ($kelas) {
            $siswas = Siswa::where('kelas_id', $kelas)
                ->with('user')
                ->get();
        }

        return view('Admin.monitoring.inputkehadiran', compact(
            'listKelas',
            'siswas',
            'kelas',
            'tanggal'
        ));
    }

    public function storeKehadiranBatch(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kehadiran' => 'required|array',
            'kehadiran.*.siswa_id' => 'required|exists:siswa,id',
            'kehadiran.*.status' => 'required|in:hadir,izin,sakit,alpa',
        ]);

        foreach ($request->kehadiran as $data) {
            // Cek duplikasi
            $exists = Kehadiran::where('siswa_id', $data['siswa_id'])
                ->where('tanggal', $request->tanggal)
                ->first();

            if ($exists) {
                $exists->update([
                    'status' => $data['status'],
                    'keterangan' => $data['keterangan'] ?? null
                ]);
            } else {
                Kehadiran::create([
                    'siswa_id' => $data['siswa_id'],
                    'tanggal' => $request->tanggal,
                    'status' => $data['status'],
                    'keterangan' => $data['keterangan'] ?? null
                ]);
            }
        }

        return back()->with('success', 'Data kehadiran berhasil disimpan!');
    }
}
