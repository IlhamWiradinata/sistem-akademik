<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\KelasSiswa;
use App\Models\Jurusan;
use App\Models\Administrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DataMasterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DATA MASTER ADMINISTRATOR
    |--------------------------------------------------------------------------
    | Menampilkan, menambah, mengupdate, dan menghapus data administrator
    |
    */

    /**
     * Menampilkan daftar administrator
     */
    public function admin()
    {
        $admin = User::where('role', 'Administrator')
                     ->with('adminProfile')
                     ->orderBy('name', 'asc')
                     ->get();

        return view('Admin.datamaster.admin', compact('admin'));
    }

    /**
     * Menyimpan data administrator baru
     */
    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email',
            'password' => 'required|min:6',
            'nip'      => 'nullable|string|max:50',
            'jabatan'  => 'nullable|string|max:100',
            'no_hp'    => 'nullable|string|max:20',
            'alamat'   => 'nullable|string|max:255',
        ]);

        // Buat akun user terlebih dahulu
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => 'Administrator',
            'password' => Hash::make($request->password),
        ]);

        // Buat profil administrator
        Administrator::create([
            'user_id' => $user->id,
            'nip'     => $request->nip,
            'jabatan' => $request->jabatan,
            'no_hp'   => $request->no_hp,
            'alamat'  => $request->alamat,
        ]);

        return redirect()->route('dataMaster.admin')
                         ->with('success', 'Data admin berhasil ditambahkan.');
    }

    /**
     * Mengupdate data administrator
     */
    public function updateAdmin(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email',
            'password' => 'nullable|min:6',
            'nip'    => 'nullable|unique:administrator,nip',
            'jabatan'  => 'nullable|string',
            'no_hp'    => 'nullable|string',
            'alamat'   => 'nullable|string',
        ]);

        // Update data user
        $user->name  = $request->name;
        $user->email = $request->email;

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Update profil administrator
        $user->adminProfile->update([
            'nip'   => $request->nip,
            'jabatan' => $request->jabatan,
            'no_hp'   => $request->no_hp,
            'alamat'  => $request->alamat,
        ]);

        return redirect()->route('dataMaster.admin')
                         ->with('success', 'Data administrator berhasil diperbarui.');
    }

    /**
     * Menghapus data administrator
     */
    public function deleteAdmin($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('dataMaster.admin')
                         ->with('success', 'Data admin berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | DATA MASTER GURU
    |--------------------------------------------------------------------------
    | Menampilkan, menambah, mengupdate, dan menghapus data guru
    | Dilengkapi dengan fitur filtering dan pencarian
    |
    */

    /**
     * Menampilkan daftar guru dengan opsi filtering
     */
    public function guru(Request $request)
    {
        $query = User::where('role', 'Guru')
                ->with(['guruProfile.jadwalKelas']);

        // Filter berdasarkan bidang keahlian
        if ($request->filled('bidang_keahlian')) {
            $query->whereHas('guruProfile', function($q) use ($request) {
                $q->where('bidang_keahlian', 'LIKE', '%' . $request->bidang_keahlian . '%');
            });
        }

        // Pencarian berdasarkan nama, email, nip, bidang keahlian, atau no HP
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('email', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('guruProfile', function($subq) use ($search) {
                      $subq->where('nip', 'LIKE', '%' . $search . '%')
                           ->orWhere('bidang_keahlian', 'LIKE', '%' . $search . '%')
                           ->orWhere('no_hp', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        // Sorting
        $sortField = $request->get('sort_field', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');

        $allowedSortFields = ['name', 'email', 'created_at'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        }

        // Pagination
        $guru = $query->orderBy('name', 'asc')->paginate(25);

        // Response untuk request AJAX
        if ($request->ajax()) {
            return response()->json([
                'data'          => $guru->items(),
                'total'         => $guru->total(),
                'current_page'  => $guru->currentPage(),
                'last_page'     => $guru->lastPage(),
                'per_page'      => $guru->perPage()
            ]);
        }

        // Data untuk dropdown filter
        $bidangKeahlianList = Guru::distinct('bidang_keahlian')
            ->whereNotNull('bidang_keahlian')
            ->pluck('bidang_keahlian')
            ->sort();

        return view('Admin.datamaster.guru', compact('guru', 'bidangKeahlianList'));
    }

    /**
     * API endpoint untuk mendapatkan data guru terfilter (JSON)
     */
    public function getGuruFiltered(Request $request)
    {
        $query = User::where('role', 'Guru')->with('guruProfile');

        // Filter berdasarkan bidang keahlian
        if ($request->filled('bidang_keahlian')) {
            $query->whereHas('guruProfile', function($q) use ($request) {
                $q->where('bidang_keahlian', $request->bidang_keahlian);
            });
        }

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('email', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('guruProfile', function($subq) use ($search) {
                      $subq->where('nip', 'LIKE', '%' . $search . '%')
                           ->orWhere('bidang_keahlian', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $guru = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $guru
        ]);
    }

    /**
     * API endpoint untuk mendapatkan daftar bidang keahlian (JSON)
     */
    public function getBidangKeahlianList()
    {
        $bidangKeahlian = Guru::distinct('bidang_keahlian')
            ->whereNotNull('bidang_keahlian')
            ->pluck('bidang_keahlian')
            ->values();

        return response()->json([
            'success' => true,
            'data'    => $bidangKeahlian
        ]);
    }

    /**
     * Menyimpan data guru baru
     */
    public function storeGuru(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email',
            'password'          => 'required|min:8',
            'nip'             => 'nullable|unique:guru,nip',
            'bidang_keahlian'   => 'required',
            'no_hp'             => 'nullable|string|max:20',
            'alamat'            => 'nullable|string|max:255',
        ]);

        // Buat akun user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => 'Guru',
            'password' => Hash::make($request->password),
        ]);

        // Buat profil guru
        Guru::create([
            'user_id'           => $user->id,
            'nip'             => $request->nip,
            'bidang_keahlian'   => $request->bidang_keahlian,
            'no_hp'             => $request->no_hp,
            'alamat'            => $request->alamat,
        ]);

        return redirect()->route('dataMaster.guru')
                         ->with('success', 'Data guru berhasil ditambahkan.');
    }

    /**
     * Mengupdate data guru
     */
    public function updateGuru(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'              => 'required|string',
            'email'             => 'required|email|unique:users,email,' . $user->id,
            'password'          => 'nullable|min:8',
            'nip'             => 'nullable|string|unique:guru,nip,' . optional($user->guruProfile)->id,
            'bidang_keahlian'   => 'nullable|string',
            'no_hp'             => 'nullable|string',
            'alamat'            => 'nullable|string',
        ]);

        // Update data user
        $user->name  = $request->name;
        $user->email = $request->email;

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Update atau buat profil guru
        $user->guruProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'nip'           => $request->nip,
                'bidang_keahlian' => $request->bidang_keahlian,
                'no_hp'           => $request->no_hp,
                'alamat'          => $request->alamat,
            ]
        );

        return redirect()->route('dataMaster.guru')
                         ->with('success', 'Data guru berhasil diperbarui.');
    }

    /**
     * Menghapus data guru
     */
    public function deleteGuru($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('dataMaster.guru')
                         ->with('success', 'Data guru berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | DATA MASTER SISWA
    |--------------------------------------------------------------------------
    | Menampilkan, menambah, mengupdate, dan menghapus data siswa
    | Termasuk manajemen kelas dan riwayat kelas siswa
    |
    */

    /**
     * Menampilkan daftar siswa
     */
    public function siswa(Request $request)
    {
        $query = User::where('role', 'Siswa')
            ->with([
                'siswaProfile.kelas',
                'siswaProfile.jurusan',
                'siswaProfile.kelasAktif.kelas',
                'siswaProfile.kelasAktif.kelas.jurusan'
            ]);

        // Filter berdasarkan kelas
        if ($request->filled('filter_kelas')) {
            $kelasId = $request->filter_kelas;
            $query->whereHas('siswaProfile.kelasAktif', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        // Filter berdasarkan jurusan
        if ($request->filled('filter_jurusan')) {
            $jurusanId = $request->filter_jurusan;
            $query->whereHas('siswaProfile.kelasAktif.kelas', function ($q) use ($jurusanId) {
                $q->where('jurusan_id', $jurusanId);
            });
        }

        // Pencarian (nama, email, nis)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhereHas('siswaProfile', function ($sub) use ($search) {
                    $sub->where('nis', 'like', "%{$search}%");
                });
            });
        }

        // Pagination: 10 data per halaman
        $siswa = $query->orderBy('name', 'asc')->paginate(25);

        // Data untuk dropdown filter
        $kelas   = Kelas::with('jurusan')->get();
        $jurusan = Jurusan::all();

        return view('Admin.datamaster.siswa', compact('siswa', 'kelas', 'jurusan'));
    }

    /**
     * Menyimpan data siswa baru
     */
    public function storeSiswa(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|min:6',
            'nis'            => 'required|unique:siswa,nis',
            'jenis_kelamin'   => 'nullable|in:Laki-laki,Perempuan',
            'kelas_id'        => 'required|exists:kelas,id',
            'jurusan_id'      => 'required|exists:jurusan,id',
            'semester'        => 'required|in:Ganjil,Genap',
            'no_hp'           => 'nullable|string',
            'alamat'          => 'nullable|string',
            'tempat_lahir'    => 'nullable|string',
            'tanggal_lahir'   => 'nullable|date',
        ]);

        // Buat akun user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => 'Siswa',
            'password' => Hash::make($request->password),
        ]);

        // Buat profil siswa
        $siswa = Siswa::create([
            'user_id'        => $user->id,
            'nis'           => $request->nis,
            'jenis_kelamin'  => $request->jenis_kelamin,
            'kelas_id'       => $request->kelas_id,
            'no_hp'          => $request->no_hp,
            'jurusan_id'     => $request->jurusan_id,
            'alamat'         => $request->alamat,
            'tempat_lahir'   => $request->tempat_lahir,
            'tanggal_lahir'  => $request->tanggal_lahir,
        ]);

        // Ambil tahun ajaran dari kelas
        $kelas = Kelas::findOrFail($request->kelas_id);

        // Buat riwayat kelas siswa
        KelasSiswa::create([
            'siswa_id'      => $siswa->id,
            'kelas_id'      => $kelas->id,
            'semester'      => $request->semester,
            'tahun_ajaran'  => $kelas->tahun_ajaran,
        ]);

        return redirect()->route('dataMaster.siswa')
                         ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Mengupdate data siswa
     */
    public function updateSiswa(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Pastikan user benar-benar siswa
        if ($user->role !== 'Siswa') {
            return redirect()->back()->with('error', 'User yang dipilih bukan siswa.');
        }

        $siswa = $user->siswaProfile;

        // Jika profil siswa tidak ditemukan, beri pesan error
        if (!$siswa) {
            return redirect()->back()->with('error', 'Data profil siswa tidak ditemukan. Kemungkinan data tidak konsisten.');
        }

        $request->validate([
            'name'            => 'required|string',
            'email'           => 'required|email|unique:users,email,' . $user->id,
            'password'        => 'nullable|min:8',
            'nis'            => 'nullable|unique:siswa,nis,' . $siswa->id,
            'jenis_kelamin'   => 'nullable|in:Laki-laki,Perempuan',
            'semester'        => 'required|in:Ganjil,Genap',
            'kelas_baru_id'   => 'nullable|exists:kelas,id',
            'no_hp'           => 'nullable|string',
            'alamat'          => 'nullable|string',
            'tempat_lahir'    => 'nullable|string',
            'tanggal_lahir'   => 'nullable|date',
        ]);

        // Update data user
        $updateUserData = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $updateUserData['password'] = Hash::make($request->password);
        }

        $user->update($updateUserData);

        // Data untuk update profil siswa
        $updateDataSiswa = [
            'nis'           => $request->nis,
            'jenis_kelamin'  => $request->jenis_kelamin,
            'no_hp'          => $request->no_hp,
            'alamat'         => $request->alamat,
            'tempat_lahir'   => $request->tempat_lahir,
            'tanggal_lahir'  => $request->tanggal_lahir,
        ];

        // Proses pindah kelas jika ada
        if ($request->filled('kelas_baru_id')) {
            $kelasBaru = Kelas::with('jurusan')->findOrFail($request->kelas_baru_id);

            // Update kelas_id dan jurusan_id di tabel siswa
            $updateDataSiswa['kelas_id']    = $kelasBaru->id;
            $updateDataSiswa['jurusan_id']  = $kelasBaru->jurusan_id;

            // Update semester di kelas aktif yang sekarang (jika ada)
            if ($siswa->kelasAktif) {
                $siswa->kelasAktif->update([
                    'semester' => $request->semester,
                ]);
            }

            // Buat entri baru di kelas_siswa untuk kelas baru
            KelasSiswa::create([
                'siswa_id'      => $siswa->id,
                'kelas_id'      => $kelasBaru->id,
                'semester'      => $request->semester,
                'tahun_ajaran'  => $kelasBaru->tahun_ajaran,
            ]);
        } else {
            // Jika tidak pindah kelas, hanya update semester
            if ($siswa->kelasAktif) {
                $siswa->kelasAktif->update([
                    'semester' => $request->semester,
                ]);
            }
        }

        // Update data siswa
        $siswa->update($updateDataSiswa);

        return redirect()->route('dataMaster.siswa')
                         ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Menghapus data siswa
     */
    public function deleteSiswa($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('dataMaster.siswa')
                         ->with('success', 'Data siswa berhasil dihapus.');
    }
}
