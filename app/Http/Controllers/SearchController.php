<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Jurusan;

class SearchController extends Controller
{
    public function adminSearch(Request $request)
    {
        $query = $request->input('q', '');

        if (empty($query)) {
            return redirect()->back()->with('warning', 'Masukkan kata kunci pencarian.');
        }

        // Pencarian di tabel User (TANPA relasi roles)
        $user = User::where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->limit(20)
            ->get();

        // Pencarian di tabel Siswa (dengan relasi user)
        $siswa = Siswa::where(function($q) use ($query) {
                $q->where('nis', 'LIKE', "%{$query}%")
                  ->orWhereHas('user', function($userQuery) use ($query) {
                      $userQuery->where('name', 'LIKE', "%{$query}%");
                  });
            })
            ->with(['user', 'jurusan'])
            ->limit(20)
            ->get();

        // Pencarian di tabel Guru (dengan relasi user)
        $guru = Guru::where(function($q) use ($query) {
                $q->where('nip', 'LIKE', "%{$query}%")
                  ->orWhere('bidang_keahlian', 'LIKE', "%{$query}%")
                  ->orWhereHas('user', function($userQuery) use ($query) {
                      $userQuery->where('name', 'LIKE', "%{$query}%");
                  });
            })
            ->with(['user'])
            ->limit(20)
            ->get();

        // Pencarian di tabel Kelas
        $kelas = Kelas::where(function($q) use ($query) {
                $q->where('nama_kelas', 'LIKE', "%{$query}%")
                  ->orWhere('tahun_ajaran', 'LIKE', "%{$query}%");
            })
            ->with(['jurusan'])
            ->limit(20)
            ->get();

        // Pencarian di tabel Jurusan
        $jurusan = Jurusan::where('nama_jurusan', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get();

        $totalResults = $user->count() + $siswa->count() + $guru->count() + $kelas->count() + $jurusan->count();

        return view('Admin.search-result', compact(
            'query',
            'user',
            'siswa',
            'guru',
            'kelas',
            'jurusan',
            'totalResults'
        ));
    }

    public function guruIndex(Request $request)
    {
        $query = $request->input('q', '');

        if (empty($query)) {
            return redirect()->back()->with('warning', 'Masukkan kata kunci pencarian.');
        }

        $guruId = auth()->user()->guruProfile->id ?? null;

        if (!$guruId) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        // Guru hanya bisa melihat siswa di kelasnya
        $siswa = Siswa::whereHas('kelasSiswa.kelas', function($q) use ($guruId) {
                $q->where('wali_kelas_id', $guruId);
            })
            ->where(function($q) use ($query) {
                $q->where('nis', 'LIKE', "%{$query}%")
                  ->orWhereHas('user', function($userQuery) use ($query) {
                      $userQuery->where('name', 'LIKE', "%{$query}%");
                  });
            })
            ->with(['user'])
            ->limit(20)
            ->get();

        // Guru bisa melihat kelas yang dia ajar
        $kelas = Kelas::where('wali_kelas_id', $guruId)
            ->where(function($q) use ($query) {
                $q->where('nama_kelas', 'LIKE', "%{$query}%")
                  ->orWhere('tahun_ajaran', 'LIKE', "%{$query}%");
            })
            ->with('jurusan')
            ->limit(10)
            ->get();

        $totalResults = $siswa->count() + $kelas->count();

        return view('Guru.search-result', compact(
            'query',
            'siswa',
            'kelas',
            'totalResults'
        ));
    }

    public function siswaIndex(Request $request)
    {
        $query = $request->input('q', '');

        if (empty($query)) {
            return redirect()->back()->with('warning', 'Masukkan kata kunci pencarian.');
        }

        $siswaId = auth()->user()->siswaProfile->id ?? null;

        if (!$siswaId) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        // Siswa hanya bisa melihat data sendiri
        $siswa = Siswa::where('id', $siswaId)
            ->where(function($q) use ($query) {
                $q->where('nis', 'LIKE', "%{$query}%")
                  ->orWhereHas('user', function($userQuery) use ($query) {
                      $userQuery->where('name', 'LIKE', "%{$query}%");
                  });
            })
            ->with(['user', 'jurusan'])
            ->get();

        // Guru/wali kelas siswa
        $guru = Guru::whereHas('kelas.siswa', function($q) use ($siswaId) {
                $q->where('id', $siswaId);
            })
            ->where(function($q) use ($query) {
                $q->where('nip', 'LIKE', "%{$query}%")
                  ->orWhereHas('user', function($userQuery) use ($query) {
                      $userQuery->where('name', 'LIKE', "%{$query}%");
                  });
            })
            ->with('user')
            ->limit(10)
            ->get();

        // Kelas siswa
        $kelas = Kelas::whereHas('siswa', function($q) use ($siswaId) {
                $q->where('id', $siswaId);
            })
            ->where(function($q) use ($query) {
                $q->where('nama_kelas', 'LIKE', "%{$query}%")
                  ->orWhere('tahun_ajaran', 'LIKE', "%{$query}%");
            })
            ->with(['jurusan'])
            ->get();

        $totalResults = $siswa->count() + $guru->count() + $kelas->count();

        return view('Siswa.search-result', compact(
            'query',
            'siswa',
            'guru',
            'kelas',
            'totalResults'
        ));
    }
}
