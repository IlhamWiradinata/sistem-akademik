<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Nilai;
use App\Models\Kehadiran;
use App\Models\PrestasiSiswa;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PrestasiSiswaController extends Controller
{
    // =========================================================
    //  DECISION TREE — Ambang Batas (Threshold) dan Bobot
    //
    //  Komponen penilaian dan bobotnya:
    //    - Nilai Akademik (rata-rata) >= 75   → bobot 50%
    //    - Persentase Kehadiran       >= 75%  → bobot 30%
    //    - Nilai Sikap/Perilaku       >= 75   → bobot 20%
    //      (skala sikap: A=90, B=75, C=60, D=45, E=30;
    //       ambang batas 75 berarti minimal predikat B)
    //
    //  SKOR AKHIR (skor_dt / skor_total, rentang 0-100):
    //  Setiap kategori hasil pohon keputusan diberi rentang skor
    //  tersendiri (selebar 20 poin) agar urutan kategori selalu
    //  konsisten dengan rentang skor_total-nya. Skor proporsional
    //  (hasil perhitungan bobot 50/30/20 atas nilai aktual siswa)
    //  digunakan sebagai posisi relatif di dalam rentang kategori
    //  tersebut, sehingga berfungsi sebagai pembeda (tie-breaker)
    //  yang konsisten secara makna.
    // =========================================================
    private const DT_NILAI_THR    = 75;
    private const DT_HADIR_THR    = 75;
    private const DT_PERILAKU_THR = 75;   // setara predikat B atau lebih tinggi

    private const BOBOT_NILAI    = 0.50;
    private const BOBOT_HADIR    = 0.30;
    private const BOBOT_PERILAKU = 0.20;

    // =========================================================
    //  Kategori Hasil Decision Tree (Predikat Capaian Siswa)
    //
    //  Predikat disusun mengikuti istilah laporan capaian belajar
    //  yang umum digunakan di lingkungan pendidikan formal, dengan
    //  penekanan pada aspek perkembangan siswa.
    //
    //  Urutan dari predikat tertinggi ke terendah:
    //    1. Berprestasi Unggul          (rentang skor 80-100)
    //    2. Berprestasi Baik             (rentang skor 60-80)
    //    3. Berkembang Sesuai Harapan    (rentang skor 40-60)
    //    4. Berkembang dengan Bimbingan  (rentang skor 20-40)
    //    5. Memerlukan Pembinaan Khusus  (rentang skor 0-20)
    // =========================================================
    private const KATEGORI_UNGGUL              = 'Berprestasi Unggul';
    private const KATEGORI_BAIK                = 'Berprestasi Baik';
    private const KATEGORI_SESUAI_HARAPAN      = 'Berkembang Sesuai Harapan';
    private const KATEGORI_DENGAN_BIMBINGAN    = 'Berkembang dengan Bimbingan';
    private const KATEGORI_PEMBINAAN_KHUSUS    = 'Memerlukan Pembinaan Khusus';

    // Rentang skor_total untuk masing-masing kategori (lebar 20 poin)
    private const RANGE_KATEGORI = [
        self::KATEGORI_PEMBINAAN_KHUSUS => [0, 20],
        self::KATEGORI_DENGAN_BIMBINGAN => [20, 40],
        self::KATEGORI_SESUAI_HARAPAN   => [40, 60],
        self::KATEGORI_BAIK             => [60, 80],
        self::KATEGORI_UNGGUL           => [80, 100],
    ];

    // =========================================================
    //  Decision Tree: tiga simpul keputusan bertingkat
    //  menghasilkan kategori (predikat) dan skor akhir (0-100)
    //
    //  Struktur pohon keputusan:
    //
    //            [Nilai >= 75?]
    //           /              \
    //         Tidak              Ya
    //   [Hadir >= 75?]       [Hadir >= 75?]
    //    /         \            /        \
    //  Tidak       Ya         Tidak       Ya
    //   |           |           |     [Sikap >= B?]
    //  Memerlukan  [Sikap     Berkembang   /        \
    //  Pembinaan    >= B?]    dengan    Berprestasi  Berprestasi
    //  Khusus      /     \   Bimbingan    Baik         Unggul
    //          Berkembang  Berkembang
    //          dengan      Sesuai
    //          Bimbingan   Harapan
    // =========================================================
    private function decisionTree(float $nilai, float $hadir, float $perilaku): array
    {
        $nilaiOk    = $nilai    >= self::DT_NILAI_THR;
        $hadirOk    = $hadir    >= self::DT_HADIR_THR;
        $perilakuOk = $perilaku >= self::DT_PERILAKU_THR;

        // Penentuan kategori berdasarkan jalur pohon keputusan
        if (!$nilaiOk && !$hadirOk) {
            $kategori = self::KATEGORI_PEMBINAAN_KHUSUS;
        } elseif (!$nilaiOk && $hadirOk) {
            $kategori = $perilakuOk ? self::KATEGORI_SESUAI_HARAPAN : self::KATEGORI_DENGAN_BIMBINGAN;
        } elseif ($nilaiOk && !$hadirOk) {
            $kategori = self::KATEGORI_DENGAN_BIMBINGAN;
        } else {
            $kategori = $perilakuOk ? self::KATEGORI_UNGGUL : self::KATEGORI_BAIK;
        }

        // Langkah 1: hitung skor proporsional mentah (0-100)
        // berdasarkan bobot Nilai 50%, Kehadiran 30%, Sikap 20%
        $kontribNilai    = ($nilai    / 100) * self::BOBOT_NILAI    * 100;
        $kontribHadir    = ($hadir    / 100) * self::BOBOT_HADIR    * 100;
        $kontribPerilaku = ($perilaku / 100) * self::BOBOT_PERILAKU * 100;

        $skorProporsional = $kontribNilai + $kontribHadir + $kontribPerilaku;

        // Langkah 2: petakan skor proporsional (0-100) ke dalam
        // rentang skor kategori, agar skor_total selalu selaras
        // dengan predikat kategori_dt yang dihasilkan
        [$batasBawah, $batasAtas] = self::RANGE_KATEGORI[$kategori];

        $posisiRelatif = $skorProporsional / 100; // bernilai 0 s.d. 1
        $skorDT = $batasBawah + ($posisiRelatif * ($batasAtas - $batasBawah));
        $skorDT = round(min(max($skorDT, $batasBawah), $batasAtas), 2);

        return [
            'kategori'    => $kategori,
            'skor_dt'     => $skorDT,
            'nilai_ok'    => $nilaiOk,
            'hadir_ok'    => $hadirOk,
            'perilaku_ok' => $perilakuOk,
        ];
    }

    // =========================================================
    //  Bantuan: ekstraksi tingkat kelas dari nama_kelas
    //  Contoh: "X IPA 1" → "X", "XI IPS 2" → "XI", "XII MIPA" → "XII"
    // =========================================================
    private function ekstrakTingkat(string $namaKelas): string
    {
        $upper = strtoupper(trim($namaKelas));
        if (str_starts_with($upper, 'XII')) return 'XII';
        if (str_starts_with($upper, 'XI'))  return 'XI';
        if (str_starts_with($upper, 'X'))   return 'X';
        return '';
    }

    // =========================================================
    //  Bantuan: hitung seluruh metrik satu siswa → hasil DT
    //  dan skor akhir
    // =========================================================
    private function hitungMetrik(int $siswaId, string $nis, string $semester, string $tahunAjaran): array
    {
        // 1. Nilai rata-rata dan jumlah mata pelajaran
        $nilaiData = Nilai::where('nis', $nis)
            ->where('semester', $semester)
            ->selectRaw('AVG(rata_rata) as rata, COUNT(DISTINCT id_mata_pelajaran) as jml')
            ->first();

        $nilaiRataRata = $nilaiData ? (float) $nilaiData->rata : 0;
        $jumlahMapel   = $nilaiData ? (int) $nilaiData->jml : 0;

        // 2. Persentase kehadiran
        $totalPertemuan = Kehadiran::where('siswa_id', $siswaId)
            ->where('semester', $semester)
            ->count();

        $totalHadir = Kehadiran::where('siswa_id', $siswaId)
            ->where('semester', $semester)
            ->where('status', 'Hadir')
            ->count();

        $pctHadir = $totalPertemuan > 0
            ? round(($totalHadir / $totalPertemuan) * 100, 2)
            : 0;

        // 3. Nilai sikap/perilaku (rata-rata numerik dari predikat sikap)
        //    Konversi: predikat huruf → nilai numerik (untuk dirata-rata)
        $nilaiPerilaku = (float) (Nilai::where('nis', $nis)
            ->where('semester', $semester)
            ->selectRaw("AVG(CASE
                WHEN UPPER(sikap) = 'A' THEN 90
                WHEN UPPER(sikap) = 'B' THEN 75
                WHEN UPPER(sikap) = 'C' THEN 60
                WHEN UPPER(sikap) = 'D' THEN 45
                WHEN UPPER(sikap) = 'E' THEN 30
                ELSE 60
            END) as np")
            ->value('np') ?? 60.0);

        // Konversi balik: nilai numerik rata-rata → predikat huruf final
        $sikapHuruf = $this->numericToSikapLetter($nilaiPerilaku);

        // 4. Decision tree: kategori (predikat) dan skor akhir
        $dt = $this->decisionTree($nilaiRataRata, $pctHadir, $nilaiPerilaku);

        return [
            'nilai_rata_rata'      => round($nilaiRataRata, 2),
            'jumlah_mapel'         => $jumlahMapel,
            'persentase_kehadiran' => $pctHadir,
            'nilai_perilaku'       => round($nilaiPerilaku, 2),
            'sikap'                => $sikapHuruf,
            'kategori_dt'          => $dt['kategori'],
            'skor_dt'              => $dt['skor_dt'],
            'skor_total'           => $dt['skor_dt'],
        ];
    }

    // Konversi nilai numerik perilaku menjadi predikat huruf (A-E)
    private function numericToSikapLetter(float $nilai): string
    {
        if ($nilai >= 90) return 'A';
        if ($nilai >= 75) return 'B';
        if ($nilai >= 60) return 'C';
        if ($nilai >= 45) return 'D';
        return 'E';
    }

    // =========================================================
    //  Pemeriksaan kesiapan data sebelum proses Juara Umum
    //  Memastikan seluruh kelas pada suatu tingkat telah memiliki
    //  data ranking kelas yang aktif
    // =========================================================
    public function checkJuaraReadiness(Request $request)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string',
            'semester'     => 'required|in:Ganjil,Genap',
            'tingkat'      => 'required|in:X,XI,XII',
        ]);

        $tahunAjaran = $request->tahun_ajaran;
        $semester    = $request->semester;
        $tingkat     = $request->tingkat;

        $semuaKelas = Kelas::where('tahun_ajaran', $tahunAjaran)
            ->get(['id', 'nama_kelas']);

        $kelasTingkat = $semuaKelas->filter(function ($k) use ($tingkat) {
            return $this->ekstrakTingkat($k->nama_kelas) === $tingkat;
        });

        $belumRanking = [];
        foreach ($kelasTingkat as $k) {
            $ada = PrestasiSiswa::where('kelas_id', $k->id)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->where('jenis_prestasi', 'ranking_kelas')
                ->where('status', 'aktif')
                ->exists();
            if (!$ada) {
                $belumRanking[] = $k->nama_kelas;
            }
        }

        return response()->json([
            'semua_sudah_ranking' => empty($belumRanking),
            'belum_ranking'       => $belumRanking,
            'total_kelas'         => $kelasTingkat->count(),
        ]);
    }

    // =========================================================
    //  Urutan kategori untuk keperluan pengurutan (sorting)
    //  Nilai lebih besar menunjukkan predikat yang lebih tinggi
    // =========================================================
    private function kategoriOrder(): array
    {
        return [
            self::KATEGORI_UNGGUL           => 5,
            self::KATEGORI_BAIK             => 4,
            self::KATEGORI_SESUAI_HARAPAN   => 3,
            self::KATEGORI_DENGAN_BIMBINGAN => 2,
            self::KATEGORI_PEMBINAAN_KHUSUS => 1,
        ];
    }

    // =========================================================
    //  Halaman utama
    // =========================================================
    public function index()
    {
        $tahunAjaranList = Kelas::select('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran', 'desc')
            ->pluck('tahun_ajaran');

        $tahunAjaranDefault = $tahunAjaranList->first();

        // Hanya ambil id dan nama_kelas — tidak ada kolom tingkat di tabel kelas
        $kelasList = $tahunAjaranDefault
            ? Kelas::where('tahun_ajaran', $tahunAjaranDefault)
                ->orderBy('nama_kelas')
                ->get(['id', 'nama_kelas'])
            : collect();

        return view('Admin.prestasi.index', compact('tahunAjaranList', 'kelasList'));
    }

    // =========================================================
    //  Pengambilan daftar kelas berdasarkan tahun ajaran (AJAX)
    //  Hanya mengembalikan id dan nama_kelas — tidak ada kolom tingkat
    // =========================================================
    public function getKelasByTahun(Request $request)
    {
        $kelas = Kelas::where('tahun_ajaran', $request->tahun_ajaran)
            ->orderBy('nama_kelas')
            ->get(['id', 'nama_kelas']);

        return response()->json($kelas);
    }

    // =========================================================
    //  Proses Ranking Kelas — berbasis Decision Tree
    // =========================================================
    public function prosesRankingKelas(Request $request)
    {
        try {
            $request->validate([
                'tahun_ajaran' => 'required|string',
                'semester'     => 'required|in:Ganjil,Genap',
                'kelas_id'     => 'required|exists:kelas,id',
            ]);

            $tahunAjaran = $request->tahun_ajaran;
            $semester    = $request->semester;
            $kelasId     = $request->kelas_id;

            $siswaIds = DB::table('kelas_siswa')
                ->where('kelas_id', $kelasId)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->pluck('siswa_id');

            if ($siswaIds->isEmpty()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Tidak ada siswa di kelas ini untuk semester dan tahun ajaran yang dipilih.',
                ], 404);
            }

            $rankingData = [];
            foreach ($siswaIds as $siswaId) {
                $siswa = Siswa::with('user')->find($siswaId);
                if (!$siswa) continue;

                $metrik = $this->hitungMetrik($siswaId, $siswa->nis, $semester, $tahunAjaran);

                $rankingData[] = array_merge($metrik, [
                    'siswa_id' => $siswaId,
                    'nis'      => $siswa->nis,
                    'nama'     => optional(optional($siswa)->user)->name
                                  ?? $siswa->nama_lengkap
                                  ?? '-',
                ]);
            }

            // Pengurutan: predikat kategori_dt (tingkatan) terlebih dahulu,
            // kemudian skor_total sebagai pembeda (tie-breaker)
            $order = $this->kategoriOrder();
            usort($rankingData, function ($a, $b) use ($order) {
                $oa = $order[$a['kategori_dt']] ?? 0;
                $ob = $order[$b['kategori_dt']] ?? 0;
                return $oa !== $ob
                    ? $ob <=> $oa
                    : $b['skor_total'] <=> $a['skor_total'];
            });

            DB::beginTransaction();
            try {
                PrestasiSiswa::where('kelas_id', $kelasId)
                    ->where('tahun_ajaran', $tahunAjaran)
                    ->where('semester', $semester)
                    ->where('jenis_prestasi', 'ranking_kelas')
                    ->delete();

                foreach ($rankingData as $i => $data) {
                    PrestasiSiswa::create([
                        'siswa_id'             => $data['siswa_id'],
                        'kelas_id'             => $kelasId,
                        'tahun_ajaran'         => $tahunAjaran,
                        'semester'             => $semester,
                        'jenis_prestasi'       => 'ranking_kelas',
                        'ranking'              => $i + 1,
                        'nilai_rata_rata'      => $data['nilai_rata_rata'],
                        'persentase_kehadiran' => $data['persentase_kehadiran'],
                        'nilai_perilaku'       => $data['nilai_perilaku'],
                        'sikap'                => $data['sikap'],
                        'skor_total'           => $data['skor_total'],
                        'jumlah_mapel'         => $data['jumlah_mapel'],
                        'kategori_dt'          => $data['kategori_dt'],
                        'status'               => 'aktif',
                    ]);
                }

                DB::commit();

                return response()->json([
                    'status'      => 'success',
                    'message'     => 'Ranking kelas berhasil dibuat untuk ' . count($rankingData) . ' siswa (metode Decision Tree).',
                    'data'        => $rankingData,
                    'kelas'       => optional(Kelas::find($kelasId))->nama_kelas ?? '-',
                    'total_siswa' => count($rankingData),
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => implode(', ', array_merge(...array_values($e->errors()))),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ], 500);
        }
    }

    // =========================================================
    //  Proses Juara Umum per tingkat — berbasis Decision Tree
    //  Tidak ada kolom 'tingkat' pada tabel kelas, sehingga
    //  digunakan ekstrakTingkat() dari nama_kelas
    // =========================================================
    public function prosesJuaraUmum(Request $request)
    {
        try {
            $request->validate([
                'tahun_ajaran' => 'required|string',
                'semester'     => 'required|in:Ganjil,Genap',
                'tingkat'      => 'required|in:X,XI,XII',
            ]);

            $tahunAjaran = $request->tahun_ajaran;
            $semester    = $request->semester;
            $tingkat     = $request->tingkat;

            // Ambil seluruh kelas pada tahun ajaran ini, lalu saring berdasarkan tingkat
            $semuaKelas = Kelas::where('tahun_ajaran', $tahunAjaran)
                ->orderBy('nama_kelas')
                ->get(['id', 'nama_kelas']);

            $kelasTingkat = $semuaKelas->filter(function ($k) use ($tingkat) {
                return $this->ekstrakTingkat($k->nama_kelas) === $tingkat;
            });

            if ($kelasTingkat->isEmpty()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Tidak ada kelas ditemukan untuk tingkat ' . $tingkat
                                . ' pada tahun ajaran ' . $tahunAjaran . '.'
                                . ' Pastikan nama kelas diawali dengan ' . $tingkat . ' (contoh: "' . $tingkat . ' IPA 1").',
                ], 404);
            }

            // Periksa kelas yang belum memiliki data ranking
            $belumRanking = [];
            foreach ($kelasTingkat as $k) {
                $ada = PrestasiSiswa::where('kelas_id', $k->id)
                    ->where('tahun_ajaran', $tahunAjaran)
                    ->where('semester', $semester)
                    ->where('jenis_prestasi', 'ranking_kelas')
                    ->where('status', 'aktif')
                    ->exists();
                if (!$ada) {
                    $belumRanking[] = $k->nama_kelas;
                }
            }

            if (!empty($belumRanking)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Kelas berikut belum diproses ranking-nya: '
                                . implode(', ', $belumRanking)
                                . '. Proses ranking kelas terlebih dahulu.',
                ], 422);
            }

            // Ambil seluruh data ranking dari kelas-kelas pada tingkat ini
            $kelasIds = $kelasTingkat->pluck('id');

            $semuaRanking = PrestasiSiswa::whereIn('kelas_id', $kelasIds)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->where('jenis_prestasi', 'ranking_kelas')
                ->where('status', 'aktif')
                ->with(['siswa.user', 'kelas'])
                ->get();

            if ($semuaRanking->isEmpty()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Tidak ada data ranking untuk tingkat ' . $tingkat . '.',
                ], 404);
            }

            // Untuk setiap siswa, ambil skor terbaik (jika terdaftar di lebih dari satu kelas)
            $juaraMap = [];
            foreach ($semuaRanking as $p) {
                $key = $p->siswa_id;
                if (!isset($juaraMap[$key]) || (float)$p->skor_total > $juaraMap[$key]['skor_total']) {
                    $juaraMap[$key] = [
                        'siswa_id'             => $p->siswa_id,
                        'nis'                  => optional($p->siswa)->nis ?? '-',
                        'nama'                 => optional(optional($p->siswa)->user)->name
                                                  ?? optional($p->siswa)->nama_lengkap
                                                  ?? '-',
                        'kelas'                => optional($p->kelas)->nama_kelas ?? '-',
                        'kelas_id'             => $p->kelas_id,
                        'ranking_kelas'        => $p->ranking,
                        'kategori_dt'          => $p->kategori_dt ?? '-',
                        'skor_total'           => (float) $p->skor_total,
                        'nilai_rata_rata'      => (float) $p->nilai_rata_rata,
                        'persentase_kehadiran' => (float) $p->persentase_kehadiran,
                        'nilai_perilaku'       => (float) $p->nilai_perilaku,
                    ];
                }
            }

            $juaraData = array_values($juaraMap);

            // Pengurutan mengikuti aturan yang sama dengan ranking kelas:
            // predikat kategori_dt terlebih dahulu, lalu skor_total
            $order = $this->kategoriOrder();
            usort($juaraData, function ($a, $b) use ($order) {
                $oa = $order[$a['kategori_dt']] ?? 0;
                $ob = $order[$b['kategori_dt']] ?? 0;
                return $oa !== $ob
                    ? $ob <=> $oa
                    : $b['skor_total'] <=> $a['skor_total'];
            });

            // Ambil 10 peringkat teratas
            $juaraData = array_slice($juaraData, 0, 10);

            DB::beginTransaction();
            try {
                // Hapus data juara umum sebelumnya untuk tingkat ini
                // Kolom tingkat berada pada tabel prestasi_siswa (bukan tabel kelas)
                PrestasiSiswa::where('tahun_ajaran', $tahunAjaran)
                    ->where('semester', $semester)
                    ->where('jenis_prestasi', 'juara_umum')
                    ->whereIn('kelas_id', $kelasIds)
                    ->delete();

                foreach ($juaraData as $i => $data) {
                    PrestasiSiswa::create([
                        'siswa_id'             => $data['siswa_id'],
                        'kelas_id'             => $data['kelas_id'],
                        'tahun_ajaran'         => $tahunAjaran,
                        'semester'             => $semester,
                        'jenis_prestasi'       => 'juara_umum',
                        'tingkat'              => $tingkat,
                        'ranking'              => $i + 1,
                        'nilai_rata_rata'      => $data['nilai_rata_rata'],
                        'persentase_kehadiran' => $data['persentase_kehadiran'],
                        'nilai_perilaku'       => $data['nilai_perilaku'],
                        'skor_total'           => $data['skor_total'],
                        'kategori_dt'          => $data['kategori_dt'],
                        'status'               => 'aktif',
                    ]);
                }

                DB::commit();

                return response()->json([
                    'status'      => 'success',
                    'message'     => 'Juara umum tingkat ' . $tingkat
                                    . ' berhasil ditetapkan (10 peringkat teratas, metode Decision Tree).',
                    'data'        => $juaraData,
                    'tingkat'     => $tingkat,
                    'total_juara' => count($juaraData),
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => implode(', ', array_merge(...array_values($e->errors()))),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ], 500);
        }
    }

    // =========================================================
    //  Halaman Hasil Prestasi
    // =========================================================
    public function hasilRanking(Request $request)
    {
        $tahunAjaranList = Kelas::select('tahun_ajaran')
            ->distinct()->orderBy('tahun_ajaran', 'desc')->pluck('tahun_ajaran');

        $tahunAjaran = $request->tahun_ajaran ?? $tahunAjaranList->first();
        $semester    = $request->semester ?? 'Ganjil';
        $jenis       = $request->jenis ?? 'ranking_kelas';
        $tingkat     = $request->tingkat ?? 'X';

        $query = PrestasiSiswa::with(['siswa.user', 'kelas'])
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->where('status', 'aktif');

        $kelasTerpilih = null;

        if ($jenis === 'ranking_kelas') {
            $query->where('jenis_prestasi', 'ranking_kelas');
            if ($request->filled('kelas_id')) {
                $query->where('kelas_id', $request->kelas_id);
                $kelasTerpilih = Kelas::find($request->kelas_id);
            }
        } else {
            $query->where('jenis_prestasi', 'juara_umum');
            if ($request->filled('tingkat')) {
                // Penyaringan menggunakan kolom tingkat pada tabel prestasi_siswa
                $query->where('tingkat', $request->tingkat);
            }
        }

        $hasil     = $query->orderBy('ranking')->paginate(50);
        $kelasList = Kelas::where('tahun_ajaran', $tahunAjaran)
            ->orderBy('nama_kelas')->get(['id', 'nama_kelas']);

        return view('Admin.prestasi.hasil', compact(
            'hasil', 'tahunAjaranList', 'kelasList',
            'tahunAjaran', 'semester', 'jenis', 'tingkat', 'kelasTerpilih'
        ));
    }

    // =========================================================
    //  Ekspor Laporan ke PDF
    // =========================================================
    public function exportPdf(Request $request)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string',
            'semester'     => 'required|in:Ganjil,Genap',
            'jenis'        => 'required|in:ranking_kelas,juara_umum',
            'kelas_id'     => 'required_if:jenis,ranking_kelas',
            'tingkat'      => 'required_if:jenis,juara_umum',
            'format'       => 'sometimes|in:detail,ringkasan',
        ]);

        $query = PrestasiSiswa::with(['siswa.user', 'kelas'])
            ->where('tahun_ajaran', $request->tahun_ajaran)
            ->where('semester', $request->semester)
            ->where('status', 'aktif');

        if ($request->jenis === 'ranking_kelas') {
            $query->where('jenis_prestasi', 'ranking_kelas')
                ->where('kelas_id', $request->kelas_id);
            $kelas    = Kelas::find($request->kelas_id);
            $title    = 'RANKING KELAS ' . ($kelas->nama_kelas ?? '');
            $subtitle = 'Kelas: ' . ($kelas->nama_kelas ?? '');
        } else {
            $query->where('jenis_prestasi', 'juara_umum')
                ->where('tingkat', $request->tingkat);
            $title    = 'JUARA UMUM TINGKAT ' . $request->tingkat . ' (10 BESAR)';
            $subtitle = 'Tingkat: Kelas ' . $request->tingkat
                    . ' — Semua Jurusan | Metode: Decision Tree';
        }

        $data = $query->orderBy('ranking')->get();

        if ($data->isEmpty()) {
            return back()->withErrors([
                'message' => 'Tidak ada data prestasi untuk parameter yang dipilih.'
            ]);
        }

        // Berkas PDF dihasilkan secara langsung dan diunduh oleh peramban
        $pdf = Pdf::loadView('Admin.prestasi.pdf', [
            'data'          => $data,
            'title'         => $title,
            'subtitle'      => $subtitle,
            'tahun_ajaran'  => $request->tahun_ajaran,
            'semester'      => $request->semester,
            'jenis'         => $request->jenis,
            'format'        => $request->format ?? 'detail',
            'tanggal_cetak' => now()->format('d/m/Y H:i:s'),
        ])->setPaper('a4', 'portrait');

        $filename = 'Laporan_Prestasi_' . str_replace([' ', '/'], '_', $title) . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }
}
