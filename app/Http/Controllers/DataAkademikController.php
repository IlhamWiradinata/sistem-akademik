<?php

namespace App\Http\Controllers;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\JadwalKelas;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\MataPelajaran;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;

class DataAkademikController extends Controller {

    public function dataKelas(Request $request)
    {
        $totalSiswa = Siswa::count();

        $tahunSekarang = date('Y');
        $tahunAjaran = $request->tahun_ajaran
            ?? ($tahunSekarang - 1) . '/' . $tahunSekarang;
        $tingkat = $request->tingkat;

        $kelasQuery = Kelas::where('tahun_ajaran', $tahunAjaran);

        if ($tingkat) {
            $kelasQuery->where('nama_kelas', 'like', $tingkat . '%');
        }

        $totalKelas = $kelasQuery->count();

        // Hitung kelas yang belum memiliki wali kelas
        $totalKelasTanpaWali = (clone $kelasQuery)->whereNull('wali_kelas_id')->count();

        // Hitung kelas yang sudah memiliki wali kelas
        $totalKelasDenganWali = $totalKelas - $totalKelasTanpaWali;

        $kelas = $kelasQuery->orderBy('nama_kelas')
                            ->paginate(10)
                            ->withQueryString();

        $jurusan = Jurusan::all();
        $guru = Guru::all();
        $list_tahun = Kelas::select('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran', 'desc')
            ->pluck('tahun_ajaran');

        return view('Admin.dataakademik.datakelas', compact(
            'kelas',
            'jurusan',
            'guru',
            'list_tahun',
            'totalSiswa',
            'totalKelas',
            'totalKelasTanpaWali',
            'totalKelasDenganWali',
            'tahunAjaran'
        ));
    }

    public function quickView($id)
    {
        $kelas = Kelas::with(['jurusan'])->findOrFail($id);

       $siswa = Siswa::whereHas('kelasAktif', function ($q) use ($kelas) {
       $q->where('kelas_id', $kelas->id)
         ->where('tahun_ajaran', $kelas->tahun_ajaran);
       })

        ->with('user')
        ->get()
        ->sortBy(fn ($s) => $s->user->name)
        ->values();


        return response()->json([
            'kelas' => $kelas,
            'siswa' => $siswa
        ]);
    }

    public function storeKelas(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required',
            'jurusan_id' => 'required',
            'tahun_ajaran' => 'required'
        ]);

        Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'jurusan_id' => $request->jurusan_id,
            'wali_kelas_id' => $request->wali_kelas_id,
            'tahun_ajaran' => $request->tahun_ajaran
        ]);

        return back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function updateKelas(Request $request, $id)
    {
        $request->validate([
            'nama_kelas' => 'required',
            'jurusan_id' => 'required',
            'tahun_ajaran' => 'required'
        ]);

        Kelas::findOrFail($id)->update([
            'nama_kelas' => $request->nama_kelas,
            'jurusan_id' => $request->jurusan_id,
            'wali_kelas_id' => $request->wali_kelas_id,
            'tahun_ajaran' => $request->tahun_ajaran
        ]);

        return back()->with('success', 'Kelas berhasil diperbarui.');
    }

    public function deleteKelas($id)
    {
        Kelas::findOrFail($id)->delete();
        return back()->with('success', 'Kelas berhasil dihapus.');
    }

    public function export($format)
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();

        $pdf = PDF::loadView(
            'Admin.dataakademik.kelas_pdf',
            compact('kelas')
        );

        return $pdf->stream('data-kelas.pdf');
    }

    public function dataMapel(Request $request)
    {
        $query = MataPelajaran::query();

        // Filter by kelompok
        if ($request->filled('kelompok')) {
            $query->where('kelompok', $request->kelompok);
        }

        // Search by nama mapel or kode
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_mapel', 'like', "%{$search}%")
                  ->orWhere('kode_mapel', 'like', "%{$search}%");
            });
        }

        $mapel = $query->orderBy('kelompok')
                       ->orderBy('nama_mapel')
                       ->paginate(15)
                       ->withQueryString();

        // Statistik per kelompok (tanpa Umum)
        $totalMapel = MataPelajaran::count();
        $totalNormatif = MataPelajaran::where('kelompok', 'Normatif')->count();
        $totalAdaptif = MataPelajaran::where('kelompok', 'Adaptif')->count();
        $totalProduktif = MataPelajaran::where('kelompok', 'Produktif')->count();

        // List kelompok untuk filter (hanya 3)
        $list_kelompok = ['Normatif', 'Adaptif', 'Produktif'];

        return view('Admin.dataakademik.datapelajaran', compact(
            'mapel',
            'totalMapel',
            'totalNormatif',
            'totalAdaptif',
            'totalProduktif',
            'list_kelompok'
        ));
    }

    public function storeMapel(Request $request)
    {
        $request->validate([
            'kode_mapel' => 'required|unique:mata_pelajarans,kode_mapel|max:10',
            'nama_mapel' => 'required|string|max:100',
            'kelompok' => 'required|in:Normatif,Adaptif,Produktif',
            'deskripsi' => 'nullable|string',
        ]);

        MataPelajaran::create($request->all());
        return redirect()->back()->with('success', 'Mata Pelajaran berhasil ditambahkan');
    }

    public function updateMapel(Request $request, $id)
    {
        $request->validate([
            'kode_mapel' => 'required|unique:mata_pelajarans,kode_mapel,'.$id.'|max:10',
            'nama_mapel' => 'required|string|max:100',
            'kelompok' => 'required|in:Normatif,Adaptif,Produktif',
            'deskripsi' => 'nullable|string',
        ]);

        $mapel = MataPelajaran::findOrFail($id);
        $mapel->update($request->all());
        return redirect()->back()->with('success', 'Mata Pelajaran berhasil diperbarui');
    }

    public function deleteMapel($id)
    {
        $mapel = MataPelajaran::findOrFail($id);
        $mapel->delete();
        return redirect()->back()->with('success', 'Mata Pelajaran berhasil dihapus');
    }

    public function exportMapel(Request $request)
    {
        $query = MataPelajaran::query();

        if ($request->filled('kelompok')) {
            $query->where('kelompok', $request->kelompok);
        }

        $mapel = $query->orderBy('kelompok')->orderBy('nama_mapel')->get();

        $kelompokFilter = $request->kelompok ?? 'Semua Kelompok';

        $pdf = PDF::loadView(
            'Admin.dataakademik.mapel_pdf',
            compact('mapel', 'kelompokFilter')
        );

        return $pdf->stream('data-mapel-'.date('Y-m-d').'.pdf');
    }

    public function dataJurusan(Request $request)
    {
        $query = Jurusan::query();

        // Filter
        if ($request->filled('search')) {
            $query->where('nama_jurusan', 'like', '%'.$request->search.'%')
                  ->orWhere('kode_jurusan', 'like', '%'.$request->search.'%');
        }

        $jurusan = $query->orderBy('nama_jurusan')->paginate(10)->withQueryString();

        // Statistik
        $totalJurusan = Jurusan::count();
        $denganKeterangan = Jurusan::whereNotNull('keterangan')->count();
        $tanpaKeterangan = Jurusan::whereNull('keterangan')->count();

        return view('Admin.dataakademik.datajurusan', compact(
            'jurusan', 'totalJurusan', 'denganKeterangan', 'tanpaKeterangan'
        ));
    }

    // Store jurusan
    public function storeJurusan(Request $request)
    {
        $request->validate([
            'kode_jurusan' => 'required|unique:jurusan,kode_jurusan',
            'nama_jurusan' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        Jurusan::create($request->all());

        return redirect()->back()->with('success', 'Jurusan berhasil ditambahkan.');
    }

    // Edit (via AJAX)
    public function editJurusan($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        return response()->json($jurusan);
    }

    // Update
    public function updateJurusan(Request $request, $id)
    {
        $jurusan = Jurusan::findOrFail($id);

        $request->validate([
            'kode_jurusan' => 'required|unique:jurusan,kode_jurusan,'.$jurusan->id,
            'nama_jurusan' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        $jurusan->update($request->all());

        return redirect()->back()->with('success', 'Jurusan berhasil diperbarui.');
    }

    // Delete
    public function deleteJurusan($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        $jurusan->delete();

        return redirect()->back()->with('success', 'Jurusan berhasil dihapus.');
    }

    public function exportJurusan()
    {
        $jurusan = Jurusan::orderBy('nama_jurusan')->get();

        $pdf = PDF::loadView(
            'Admin.dataakademik.jurusan_pdf',
            compact('jurusan')
        );

        return $pdf->stream('data-jurusan.pdf');
    }

}
