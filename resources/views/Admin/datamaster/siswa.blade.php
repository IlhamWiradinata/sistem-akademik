@extends('Layouts.LayoutsAdmin.app')

@section('title')
<title>Sistem Akademik - Data Master Siswa</title>
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-user-graduate"></i> Data Master Siswa
    </h1>
    <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#addSiswaModal">
        <i class="fas fa-plus fa-sm"></i> Tambah Siswa
    </button>
</div>

<!-- DataTable Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table"></i> Daftar Siswa
        </h6>
        <div>
            <span class="badge badge-primary badge-pill mr-2">{{ $siswa->total() }} Siswa</span>
        </div>
    </div>

    <!-- FILTER SECTION -->
    <div class="card-header py-2 bg-light">
        <form method="GET" action="{{ route('dataMaster.siswa') }}" id="filterForm">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-primary">
                            <i class="fas fa-filter"></i> Filter Kelas
                        </label>
                        <select class="form-control form-control-sm" name="filter_kelas" id="filterKelas" onchange="this.form.submit()">
                            <option value="">Semua Kelas</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}" {{ request('filter_kelas') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-primary">
                            <i class="fas fa-filter"></i> Filter Jurusan
                        </label>
                        <select class="form-control form-control-sm" name="filter_jurusan" id="filterJurusan" onchange="this.form.submit()">
                            <option value="">Semua Jurusan</option>
                            @foreach($jurusan as $j)
                                <option value="{{ $j->id }}" {{ request('filter_jurusan') == $j->id ? 'selected' : '' }}>
                                    {{ $j->nama_jurusan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-primary">
                            <i class="fas fa-search"></i> Pencarian Cepat
                        </label>
                        <input type="text" class="form-control form-control-sm" name="search" id="searchInput" placeholder="Cari nama, email, nis..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3 text-right">
                    <div class="form-group mb-0" style="padding-top: 28px;">
                        <button type="button" class="btn btn-sm btn-secondary" onclick="resetSiswaFilters()">
                            <i class="fas fa-undo"></i> Reset Filter
                        </button>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="card-body">
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        <!-- Info Filter Aktif -->
        <div class="filter-info mb-3" id="filterInfo" style="display: none;">
            <div class="alert alert-info py-2 mb-0">
                <i class="fas fa-info-circle"></i>
                <span id="filterText">Filter aktif: </span>
                <button class="btn btn-sm btn-link text-danger" onclick="resetSiswaFilters()">
                    <i class="fas fa-times"></i> Hapus filter
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="siswaTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr class="text-center">
                        <th width="5%">No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>NIS</th>
                        <th>Jenis Kelamin</th>
                        <th>Kelas</th>
                        <th>Jurusan</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody id="siswaTableBody">
                    @forelse ($siswa as $key => $row)
                    <tr class="siswa-row">
                        <td class="text-center">{{ $loop->iteration + ($siswa->currentPage() - 1) * $siswa->perPage() }}</td>
                        <td class="siswa-nama">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle mr-2">
                                    <i class="fas fa-user-circle text-success fa-2x"></i>
                                </div>
                                <div>
                                    <strong>{{ $row->name }}</strong>
                                </div>
                            </div>
                        </td>
                        <td class="siswa-email">
                            <a href="mailto:{{ $row->email }}" class="text-decoration-none">
                                <i class="fas fa-envelope text-info"></i> {{ $row->email }}
                            </a>
                        </td>
                        <td class="text-center siswa-nis">
                            @if($row->siswaProfile && $row->siswaProfile->nis)
                                <span class="badge badge-primary">{{ $row->siswaProfile->nis }}</span>
                            @else
                                <span class="badge badge-light">-</span>
                            @endif
                        </td>
                        <td class="text-center siswa-jk">
                            @if($row->siswaProfile?->jenis_kelamin)
                                @php
                                    $jkColor = $row->siswaProfile->jenis_kelamin === 'Laki-laki' ? 'info' : 'pink';
                                @endphp
                                <span class="badge badge-{{ $jkColor }} jk-text">
                                    {{ $row->siswaProfile->jenis_kelamin }}
                                </span>
                            @else
                                <span class="badge badge-light jk-text">-</span>
                            @endif
                        </td>
                        <td class="siswa-kelas">
                        @php
                            $jurusanName = $row->siswaProfile?->kelasAktif?->kelas?->jurusan?->nama_jurusan ?? '';
                            $badgeKelasClass = match($jurusanName) {
                                'Teknik Kendaraan Ringan Otomotif' => 'badge-kelas-tkr-light',
                                'Teknik Pemesinan' => 'badge-kelas-tp-light',
                                'Rekayasa Perangkat Lunak' => 'badge-kelas-rpl-light',
                                'Teknik Komputer dan Jaringan' => 'badge-kelas-tkj-light',
                                'Akuntansi dan Keuangan Lembaga' => 'badge-kelas-akl-light',
                                default => 'badge-light'
                            };
                        @endphp

                        @if($row->siswaProfile?->kelasAktif?->kelas?->nama_kelas)
                            <span class="badge {{ $badgeKelasClass }} kelas-text">
                                {{ $row->siswaProfile->kelasAktif->kelas->nama_kelas }}
                            </span>
                            <br>
                            <small class="text-muted">
                                {{ $row->siswaProfile?->kelasAktif?->tahun_ajaran ?? '' }}
                            </small>
                        @else
                            <span class="text-muted kelas-text">-</span>
                        @endif
                    </td>
                        <td class="siswa-jurusan">
                           @php
                                $jurusanName = $row->siswaProfile?->kelasAktif?->kelas?->jurusan?->nama_jurusan ?? '';
                                $badgeJurusanClass = match($jurusanName) {
                                    'Teknik Kendaraan Ringan Otomotif' => 'badge-jurusan-tkr',
                                    'Teknik Pemesinan' => 'badge-jurusan-tp',
                                    'Rekayasa Perangkat Lunak' => 'badge-jurusan-rpl',
                                    'Teknik Komputer dan Jaringan' => 'badge-jurusan-tkj',
                                    'Akuntansi dan Keuangan Lembaga' => 'badge-jurusan-akl',
                                    default => 'badge-light'
                                };
                            @endphp

                            @if($jurusanName)
                                <span class="badge {{ $badgeJurusanClass }} jurusan-text">{{ $jurusanName }}</span>
                            @else
                                <span class="text-muted jurusan-text">-</span>
                            @endif
                        </td>
                        <td class="text-center siswa-semester">
                            @if($row->siswaProfile?->kelasAktif?->semester)
                                <span class="badge semester-text
                                    @if($row->siswaProfile->kelasAktif->semester == 'Ganjil') badge-info
                                    @else badge-primary @endif">
                                    {{ $row->siswaProfile->kelasAktif->semester }}
                                </span>
                            @else
                                <span class="badge badge-light semester-text">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $status = ($row->siswaProfile && $row->siswaProfile->kelasAktif) ? 'Aktif' : 'Tidak Aktif';
                                $badgeClass = ($status == 'Aktif') ? 'badge-success' : 'badge-secondary';
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                <i class="fas fa-circle fa-xs"></i> {{ $status }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center align-items-center" style="gap: 5px;">
                            <!-- Edit Button -->
                            <button class="btn btn-warning btn-sm btn-circle"
                                    data-toggle="modal"
                                    data-target="#editSiswaModal{{ $row->id }}"
                                    title="Edit Data">
                                <i class="fas fa-edit"></i>
                            </button>
                            <!-- Delete Button -->
                            <form action="{{ route('dataMaster.siswa.delete', $row->id) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirmDeleteSiswa()">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm btn-circle"
                                        type="submit"
                                        title="Hapus Data">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="editSiswaModal{{ $row->id }}" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <form action="{{ route('dataMaster.siswa.update', $row->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title">
                                            <i class="fas fa-edit"></i> Edit Data Siswa
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-info alert-dismissible fade show">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Info:</strong> Kosongkan field password jika tidak ingin mengubah password.
                                            <button type="button" class="close" data-dismiss="alert">
                                                <span>&times;</span>
                                            </button>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-bold">
                                                    Nama Lengkap <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="name" value="{{ $row->name }}" class="form-control" required>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-bold">
                                                    Email <span class="text-danger">*</span>
                                                </label>
                                                <input type="email" name="email" value="{{ $row->email }}" class="form-control" required>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-bold">Password Baru</label>
                                                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah" minlength="8">
                                                <small class="form-text text-muted">Minimal 8 karakter</small>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-bold">NIS</label>
                                                <input type="text" name="nis" value="{{ $row->siswaProfile->nis ?? '' }}" class="form-control">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-bold">Jenis Kelamin</label>
                                                <select name="jenis_kelamin" class="form-control">
                                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                                    <option value="Laki-laki" {{ $row->siswaProfile?->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                                    <option value="Perempuan" {{ $row->siswaProfile?->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-bold">Tahun Ajaran</label>
                                                <select class="form-control tahunAjaranEdit" data-target="{{ $row->id }}">
                                                    <option value="">Pilih Tahun Ajaran</option>
                                                    @foreach($kelas->pluck('tahun_ajaran')->unique() as $ta)
                                                        <option value="{{ $ta }}" {{ $row->siswaProfile?->kelasAktif?->tahun_ajaran == $ta ? 'selected' : '' }}>
                                                            {{ $ta }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-bold">Semester</label>
                                                <select name="semester" class="form-control">
                                                    <option value="Ganjil" {{ $row->siswaProfile?->kelasAktif?->semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                                    <option value="Genap" {{ $row->siswaProfile?->kelasAktif?->semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-bold">Pindah Kelas</label>
                                                <select name="kelas_baru_id" id="kelasEdit{{ $row->id }}" class="form-control">
                                                    <option value="">-- Tetap di Kelas Sekarang --</option>
                                                </select>
                                                <small class="form-text text-muted">Kosongkan jika tidak ingin pindah kelas</small>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-bold">No HP</label>
                                                <input type="text" name="no_hp" value="{{ $row->siswaProfile->no_hp ?? '' }}" class="form-control" placeholder="08xxxxxxxxxx">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-bold">Tempat Lahir</label>
                                                <input type="text" name="tempat_lahir" value="{{ $row->siswaProfile->tempat_lahir ?? '' }}" class="form-control" placeholder="Tempat Lahir">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-bold">Tanggal Lahir</label>
                                                <input type="date" name="tanggal_lahir" value="{{ $row->siswaProfile->tanggal_lahir ?? '' }}" class="form-control">
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <label class="font-weight-bold">Alamat</label>
                                                <textarea name="alamat" class="form-control" rows="3">{{ $row->siswaProfile->alamat ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            <i class="fas fa-times"></i> Batal
                                        </button>
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-save"></i> Simpan Perubahan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            <i class="fas fa-user-graduate fa-3x mb-3 d-block"></i>
                            <p class="mb-0">Belum ada data siswa</p>
                            <small>Silakan tambah siswa baru dengan tombol di atas</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-3 d-flex justify-content-between align-items-center">
            <div class="text-muted">
                <span class="text-muted">Menampilkan {{ $siswa->firstItem() }} - {{ $siswa->lastItem() }} dari {{ $siswa->total() }} Siswa</span>
            </div>
            <div class="d-flex justify-content-end">
                {{ $siswa->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addSiswaModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('dataMaster.siswa.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus"></i> Tambah Siswa Baru
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Informasi:</strong> Field yang bertanda <span class="text-danger">*</span> wajib diisi.
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" class="form-control" placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" class="form-control" placeholder="siswa@example.com" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">
                                Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required minlength="8">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">NIS</label>
                            <input type="text" name="nis" class="form-control" placeholder="Nomor Induk Siswa" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control">
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Tahun Ajaran <span class="text-danger">*</span></label>
                            <select id="tahunAjaranTambah" class="form-control" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                @foreach($kelas->pluck('tahun_ajaran')->unique() as $ta)
                                    <option value="{{ $ta }}">{{ $ta }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Kelas <span class="text-danger">*</span></label>
                            <select name="kelas_id" id="kelasTambah" class="form-control" required>
                                <option value="">-- Pilih Kelas --</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Jurusan <span class="text-danger">*</span></label>
                            <select name="jurusan_id" class="form-control" required>
                                <option value="">-- Pilih Jurusan --</option>
                                @foreach($jurusan as $j)
                                    <option value="{{ $j->id }}">{{ $j->nama_jurusan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Semester <span class="text-danger">*</span></label>
                            <select name="semester" class="form-control" required>
                                <option value="">-- Pilih Semester --</option>
                                <option value="Ganjil">Ganjil</option>
                                <option value="Genap">Genap</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">No HP</label>
                            <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control" placeholder="Tempat Lahir">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control">
                        </div>
                        <div class="col-md-12 form-group">
                            <label class="font-weight-bold">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3" placeholder="Alamat lengkap siswa"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge-pink {
        background-color: #e83e8c !important;
        color: white !important;
    }

    /* Badge untuk jurusan */
    .badge-jurusan-tkr { background-color: #dc3545; color: white; }  /* Merah */
    .badge-jurusan-tp  { background-color: #007bff; color: white; }  /* Biru */
    .badge-jurusan-rpl { background-color: #ffc107; color: #212529; } /* Kuning (teks gelap) */
    .badge-jurusan-tkj { background-color: #28a745; color: white; }  /* Hijau */
    .badge-jurusan-akl { background-color: #e83e8c; color: white; }  /* Pink */

    /* Opsi: jika ingin versi light untuk badge kelas */
    .badge-kelas-tkr-light { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .badge-kelas-tp-light  { background-color: #cce5ff; color: #004085; border: 1px solid #b8daff; }
    .badge-kelas-rpl-light { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
    .badge-kelas-tkj-light { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .badge-kelas-akl-light { background-color: #f8bbd0; color: #880e4f; border: 1px solid #f5c6cb; } /* atau pink light */

    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .password-strength .progress {
        background-color: #e9ecef;
    }

    .password-strength .progress-bar {
        transition: width 0.3s ease;
    }

    .filter-highlight {
        background-color: #fff3cd !important;
        transition: background-color 0.5s ease;
    }

    .filter-info {
        transition: all 0.3s ease;
    }

    #filterKelas, #filterJurusan, #filterSemester, #searchInput {
        border: 1px solid #d1d3e2;
    }

    #filterKelas:focus, #filterJurusan:focus, #filterSemester:focus, #searchInput:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
</style>
@endpush

@push('scripts')
<script>
/* ===============================
   DATA KELAS (DARI LARAVEL)
================================ */
const kelasData = @json($kelas->map(fn($k) => [
    'id' => $k->id,
    'nama' => $k->nama_kelas,
    'tahun' => $k->tahun_ajaran
]));

/* ===============================
   FUNCTION RENDER KELAS
================================ */
function renderKelas(selectEl, tahun) {
    if (!selectEl) return;

    selectEl.innerHTML = '<option value="">-- Pilih Kelas --</option>';

    kelasData
        .filter(k => k.tahun === tahun)
        .forEach(k => {
            const opt = document.createElement('option');
            opt.value = k.id;
            opt.textContent = k.nama;
            selectEl.appendChild(opt);
        });
}

/* ===============================
   FILTER SISWA
================================ */
document.addEventListener('DOMContentLoaded', function() {

    // Ambil elemen-elemen yang diperlukan
    const filterKelas = document.getElementById('filterKelas');
    const filterJurusan = document.getElementById('filterJurusan');
    const filterSemester = document.getElementById('filterSemester');
    const searchInput = document.getElementById('searchInput');
    const rows = document.querySelectorAll('.siswa-row');
    const filterInfo = document.getElementById('filterInfo');
    const filterText = document.getElementById('filterText');
    const totalRowsSpan = document.getElementById('totalRows');
    const filteredInfo = document.getElementById('filteredInfo');

    // Fungsi untuk melakukan filter
    function filterSiswaTable() {
        const kelasValue = filterKelas ? filterKelas.value.toLowerCase().trim() : '';
        const jurusanValue = filterJurusan ? filterJurusan.value.toLowerCase().trim() : '';
        const semesterValue = filterSemester ? filterSemester.value.toLowerCase().trim() : '';
        const searchValue = searchInput ? searchInput.value.toLowerCase().trim() : '';

        let visibleCount = 0;
        let filterActive = false;

        rows.forEach(row => {
            // Ambil data dari row
            const nama = row.querySelector('.siswa-nama')?.textContent.toLowerCase() || '';
            const email = row.querySelector('.siswa-email')?.textContent.toLowerCase() || '';
            const nis = row.querySelector('.siswa-nis')?.textContent.toLowerCase() || '';
            const kelas = row.querySelector('.kelas-text')?.textContent.toLowerCase() || '';
            const jurusan = row.querySelector('.jurusan-text')?.textContent.toLowerCase() || '';
            const semester = row.querySelector('.semester-text')?.textContent.toLowerCase() || '';

            // Cek filter kelas
            let matchKelas = true;
            if (kelasValue !== '') {
                matchKelas = kelas.includes(kelasValue);
                if (matchKelas) filterActive = true;
            }

            // Cek filter jurusan
            let matchJurusan = true;
            if (jurusanValue !== '') {
                matchJurusan = jurusan.includes(jurusanValue);
                if (matchJurusan) filterActive = true;
            }

            // Cek filter semester
            let matchSemester = true;
            if (semesterValue !== '') {
                matchSemester = semester.includes(semesterValue);
                if (matchSemester) filterActive = true;
            }

            // Cek pencarian
            let matchSearch = true;
            if (searchValue !== '') {
                matchSearch = nama.includes(searchValue) ||
                             email.includes(searchValue) ||
                             nis.includes(searchValue);
                if (matchSearch) filterActive = true;
            }

            // Tampilkan atau sembunyikan row
            if (matchKelas && matchJurusan && matchSemester && matchSearch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update info filter
        updateFilterInfo(filterActive, kelasValue, jurusanValue, semesterValue, searchValue, visibleCount);

        // Tampilkan pesan jika tidak ada data
        const tbody = document.getElementById('siswaTableBody');
        let noDataRow = document.getElementById('noDataRow');

        if (visibleCount === 0 && rows.length > 0) {
            if (!noDataRow) {
                noDataRow = document.createElement('tr');
                noDataRow.id = 'noDataRow';
                noDataRow.innerHTML = '<td colspan="10" class="text-center text-muted py-4"><i class="fas fa-search fa-3x mb-3 d-block"></i><p class="mb-0">Tidak ada siswa yang sesuai dengan filter</p><small>Silakan atur ulang filter</small></td>';
                tbody.appendChild(noDataRow);
            }
        } else {
            if (noDataRow) {
                noDataRow.remove();
            }
        }

        // Update jumlah data
        if (totalRowsSpan) {
            totalRowsSpan.textContent = rows.length;
        }
    }

    // Fungsi untuk update info filter
    function updateFilterInfo(isActive, kelas, jurusan, semester, search, visibleCount) {
        if (!filterInfo || !filterText || !filteredInfo) return;

        if (isActive) {
            filterInfo.style.display = 'block';
            let filterDesc = [];
            if (kelas) filterDesc.push(`Kelas: <strong>${filterKelas.value}</strong>`);
            if (jurusan) filterDesc.push(`Jurusan: <strong>${filterJurusan.value}</strong>`);
            if (semester) filterDesc.push(`Semester: <strong>${filterSemester.value}</strong>`);
            if (search) filterDesc.push(`Pencarian: <strong>"${searchInput.value}"</strong>`);
            filterText.innerHTML = 'Filter aktif: ' + filterDesc.join(', ');

            if (filteredInfo) {
                filteredInfo.innerHTML = `Menampilkan <strong>${visibleCount}</strong> dari <strong>${rows.length}</strong> data`;
            }
        } else {
            filterInfo.style.display = 'none';
            if (filteredInfo) {
                filteredInfo.innerHTML = '';
            }
        }
    }

    window.resetSiswaFilters = function() {
    // Reset semua input filter ke nilai default
    document.getElementById('filterKelas').value = '';
    document.getElementById('filterJurusan').value = '';
    document.getElementById('searchInput').value = '';
    // Jika ada filter semester, reset juga (sesuaikan jika ada)
    // document.getElementById('filterSemester').value = '';

    // Submit form untuk memuat ulang data tanpa filter
    document.getElementById('filterForm').submit();
    };

    // Event listeners untuk filter
    if (filterKelas) filterKelas.addEventListener('change', filterSiswaTable);
    if (filterJurusan) filterJurusan.addEventListener('change', filterSiswaTable);
    if (filterSemester) filterSemester.addEventListener('change', filterSiswaTable);
    if (searchInput) searchInput.addEventListener('keyup', filterSiswaTable);

    // Auto-hide alerts
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(alert => {
            if (alert.classList.contains('alert-dismissible')) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            }
        });
    }, 5000);
});

/* ===============================
   TAMBAH SISWA
================================ */
const tahunTambah = document.getElementById('tahunAjaranTambah');
const kelasTambah = document.getElementById('kelasTambah');

if (tahunTambah && kelasTambah) {
    tahunTambah.addEventListener('change', function () {
        renderKelas(kelasTambah, this.value);
    });
}

/* ===============================
   EDIT SISWA (MULTI MODAL)
================================ */
document.querySelectorAll('.tahunAjaranEdit').forEach(select => {
    const target = select.dataset.target;
    const kelasEdit = document.getElementById('kelasEdit' + target);

    if (!kelasEdit) return;

    // render pertama kali
    renderKelas(kelasEdit, select.value);

    select.addEventListener('change', function () {
        renderKelas(kelasEdit, this.value);
    });
});

function confirmDeleteSiswa() {
    return confirm('Apakah Anda yakin ingin menghapus siswa ini?');
}
</script>
@endpush
