<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalKelas;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\MataPelajaran;
use Barryvdh\DomPDF\Facade\Pdf;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        // ===============================
        // TAHUN AJARAN AKTIF
        // ===============================
        $tahunAjaranAktif = (now()->month >= 7)
            ? now()->year . '/' . (now()->year + 1)
            : (now()->year - 1) . '/' . now()->year;

        $tahunAjaran = $request->tahun_ajaran ?? $tahunAjaranAktif;

        // ===============================
        // JADWAL DENGAN PAGINATION
        // ===============================
        $jadwal = JadwalKelas::with(['kelas','guru.user','mapel'])
            ->whereHas('kelas', function ($q) use ($tahunAjaran) {
                $q->where('tahun_ajaran', $tahunAjaran);
            });

        if ($request->kelas_id) {
            $jadwal->where('kelas_id', $request->kelas_id);
        }

        if ($request->guru_id) {
            $jadwal->where('guru_id', $request->guru_id);
        }

        if ($request->mapel_id) {
            $jadwal->where('id_mata_pelajaran', $request->mapel_id);
        }

        // PERBAIKAN: Urutkan berdasarkan hari (Senin-Jumat) dan jam
        $jadwal = $jadwal->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat')")
            ->orderBy('jam_mulai')
            ->paginate(15)
            ->withQueryString();

        // ===============================
        // MASTER DATA
        // ===============================
        $kelas = Kelas::where('tahun_ajaran', $tahunAjaran)
            ->orderBy('nama_kelas')
            ->get();

        $guru  = Guru::all();
        $mapel = MataPelajaran::all();

        // ===============================
        // STATISTIK
        // ===============================
        $totalKelas  = $kelas->count();
        $totalJadwal = JadwalKelas::whereHas('kelas', function ($q) use ($tahunAjaran) {
                $q->where('tahun_ajaran', $tahunAjaran);
            })->count();
        $totalGuru   = $guru->count();
        $totalMapel  = $mapel->count();

        // LIST TAHUN AJARAN UNTUK FILTER
        $listTahunAjaran = Kelas::select('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran', 'desc')
            ->pluck('tahun_ajaran');

        return view('Admin.dataakademik.kelolajadwal', compact(
            'jadwal',
            'kelas',
            'guru',
            'mapel',
            'tahunAjaran',
            'listTahunAjaran',
            'totalKelas',
            'totalJadwal',
            'totalGuru',
            'totalMapel'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat', // HANYA Senin-Jumat
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'id_mata_pelajaran' => 'required|exists:mata_pelajarans,id',
            'guru_id' => 'nullable|exists:guru,id',
        ]);

        JadwalKelas::create($validated);

        return redirect()->route('KelolaJadwal')
            ->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalKelas::findOrFail($id);

        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat', // HANYA Senin-Jumat
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'id_mata_pelajaran' => 'required|exists:mata_pelajarans,id',
            'guru_id' => 'nullable|exists:guru,id',
        ]);

        $jadwal->update($validated);

        return redirect()->route('KelolaJadwal')
            ->with('success', 'Jadwal berhasil diperbarui!');
    }

    public function edit(JadwalKelas $jadwal)
    {
        $kelas = Kelas::all();
        $guru = Guru::all();
        $mapel = MataPelajaran::all();

        return view('Admin.dataakademik.jadwal.edit', compact('jadwal', 'kelas', 'guru', 'mapel'));
    }

    public function destroy($id)
    {
        $jadwal = JadwalKelas::findOrFail($id);
        $jadwal->delete();

        return redirect()->route('KelolaJadwal')
            ->with('success', 'Jadwal berhasil dihapus!');
    }

    public function export(Request $request)
    {
        // TAHUN AJARAN
        $tahunAjaranAktif = (now()->month >= 7)
            ? now()->year . '/' . (now()->year + 1)
            : (now()->year - 1) . '/' . now()->year;

        $tahunAjaran = $request->tahun_ajaran ?? $tahunAjaranAktif;

        // JADWAL
        $jadwal = JadwalKelas::with(['kelas','guru.user','mapel'])
            ->whereHas('kelas', function ($q) use ($tahunAjaran) {
                $q->where('tahun_ajaran', $tahunAjaran);
            });

        if ($request->kelas_id) {
            $jadwal->where('kelas_id', $request->kelas_id);
        }

        if ($request->guru_id) {
            $jadwal->where('guru_id', $request->guru_id);
        }

        if ($request->mapel_id) {
            $jadwal->where('id_mata_pelajaran', $request->mapel_id);
        }

        // PERBAIKAN: Urutkan berdasarkan hari (Senin-Jumat) dan jam
        $jadwal = $jadwal->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat')")
            ->orderBy('jam_mulai')
            ->get();

        $pdf = Pdf::loadView(
            'Admin.dataakademik.jadwal_pdf',
            compact('jadwal', 'tahunAjaran')
        );

        $filename = 'jadwal-pelajaran-' . str_replace('/', '-', $tahunAjaran) . '.pdf';

        return $pdf->stream($filename);
    }
}
