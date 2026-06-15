@extends('Layouts.LayoutsAdmin.app')

@section('title')
    <title>Sistem Akademik - Kelola Jadwal Pelajaran</title>
@endsection

@section('content')
{{-- PAGE HEADING --}}
<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0 text-gray-800">
        <i class="fas fa-calendar-alt mr-2"></i>Kelola Jadwal Pelajaran
    </h4>
    <button type="button" class="btn btn-primary mb-0" data-toggle="modal" data-target="#tambahJadwalModal">
        <i class="fas fa-plus-circle mr-1"></i>Tambah Jadwal Pelajaran
    </button>
</div>

{{-- ALERT --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm">
        <strong><i class="fas fa-exclamation-circle mr-1"></i>Terjadi kesalahan!</strong>
        <ul class="mt-2 mb-0">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

    {{-- STATISTIK CARD --}}
    <div class="row mb-4">
        <div class="col-md-3 mb-2">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Jadwal</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalJadwal }}</div>
                    </div>
                    <div class="me-3">
                        <i class="fas fa-calendar-alt fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-2">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Kelas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalKelas }}</div>
                    </div>
                    <div class="me-3">
                        <i class="fas fa-chalkboard fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-2">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Guru</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalGuru }}</div>
                    </div>
                    <div class="me-3">
                        <i class="fas fa-chalkboard-teacher fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-2">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Mapel</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalMapel }}</div>
                    </div>
                    <div class="me-3">
                        <i class="fas fa-book fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER + EXPORT --}}
    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('KelolaJadwal') }}" class="row align-items-end">
                <div class="col-md-2">
                    <label class="font-weight-bold">Tahun Ajaran</label>
                    <select name="tahun_ajaran" class="form-control" onchange="this.form.submit()">
                        @foreach($listTahunAjaran as $ta)
                            <option value="{{ $ta }}" {{ $tahunAjaran == $ta ? 'selected' : '' }}>
                                {{ $ta }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Kelas --}}
                <div class="col-md-3">
                    <label class="font-weight-bold">Kelas</label>
                    <select name="kelas_id" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Guru --}}
                <div class="col-md-3">
                    <label class="font-weight-bold">Guru</label>
                    <select name="guru_id" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Semua Guru --</option>
                        @foreach($guru as $g)
                            <option value="{{ $g->id }}" {{ request('guru_id') == $g->id ? 'selected' : '' }}>
                                {{ $g->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Mapel --}}
                <div class="col-md-3">
                    <label class="font-weight-bold">Mata Pelajaran</label>
                    <select name="mapel_id" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Semua Mapel --</option>
                        @foreach($mapel as $m)
                            <option value="{{ $m->id }}" {{ request('mapel_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->nama_mapel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Export PDF --}}
                <div class="col-md-1 text-right">
                    <label class="d-block">&nbsp;</label>
                    <a href="{{ route('jadwal.export', [
                        'tahun_ajaran' => $tahunAjaran,
                        'kelas_id' => request('kelas_id'),
                        'guru_id' => request('guru_id'),
                        'mapel_id' => request('mapel_id')
                    ]) }}" target="_blank" class="btn btn-danger btn-sm">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL JADWAL --}}
    <div class="card shadow border-0">
        <div class="card-body table-responsive">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h5 class="font-weight-bold text-gray-700">Daftar Jadwal Pelajaran</h5>
            </div>

            <table class="table table-bordered table-hover table-sm text-center">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Kelas</th>
                        <th width="10%">Hari</th>
                        <th width="15%">Jam</th>
                        <th width="25%">Guru</th>
                        <th width="20%">Mata Pelajaran</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwal as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->kelas->nama_kelas ?? '-' }}</td>
                            <td>
                                @php
                                    $hariClass = strtolower($item->hari);
                                @endphp
                                <span class="badge badge-{{ $hariClass }}">{{ $item->hari }}</span>
                            </td>
                            <td><span class="badge badge-info">{{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }}</span></td>
                            <td>
                                @if($item->guru && $item->guru->user)
                                    {{ $item->guru->user->name }}
                                @else
                                    <span class="text-danger font-weight-bold">
                                        <i class="fas fa-exclamation-circle"></i> Belum Ada Guru
                                    </span>
                                @endif
                            </td>
                            <td>{{ $item->mapel->nama_mapel ?? '-' }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal{{ $item->id }}" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('jadwal.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- MODAL EDIT --}}
                        <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <form action="{{ route('jadwal.update', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning text-white">
                                            <h5 class="modal-title"><i class="fas fa-edit mr-1"></i>Edit Jadwal</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label><strong>Kelas</strong></label>
                                                <select name="kelas_id" class="form-control" required>
                                                    @foreach($kelas as $k)
                                                        <option value="{{ $k->id }}" {{ $item->kelas_id == $k->id ? 'selected' : '' }}>
                                                            {{ $k->nama_kelas }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label><strong>Hari</strong></label>
                                                <select name="hari" class="form-control" required>
                                                    <option value="">-- Pilih Hari --</option>
                                                    @foreach(['Senin','Selasa','Rabu','Kamis','Jumat'] as $h)
                                                        <option value="{{ $h }}" {{ $item->hari == $h ? 'selected' : '' }}>
                                                            {{ $h }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">Hari Sabtu tidak tersedia</small>
                                            </div>

                                            <div class="form-group">
                                                <label><strong>Jam Mulai</strong></label>
                                                <input type="time" name="jam_mulai" class="form-control" value="{{ $item->jam_mulai }}" required>
                                            </div>

                                            <div class="form-group">
                                                <label><strong>Jam Selesai</strong></label>
                                                <input type="time" name="jam_selesai" class="form-control" value="{{ $item->jam_selesai }}" required>
                                            </div>

                                            <div class="form-group">
                                                <label><strong>Guru</strong> <small class="text-muted">(Opsional)</small></label>
                                                <select name="guru_id" class="form-control">
                                                    <option value="">-- Pilih Guru (Opsional) --</option>
                                                    @foreach($guru as $g)
                                                        <option value="{{ $g->id }}" {{ $item->guru_id == $g->id ? 'selected' : '' }}>
                                                            {{ $g->user->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">Kosongkan jika belum ada guru</small>
                                            </div>

                                            <div class="form-group">
                                                <label><strong>Mata Pelajaran</strong></label>
                                                <select name="id_mata_pelajaran" class="form-control" required>
                                                    @foreach($mapel as $m)
                                                        <option value="{{ $m->id }}" {{ $item->id_mata_pelajaran == $m->id ? 'selected' : '' }}>
                                                            {{ $m->nama_mapel }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="text-muted text-center py-4">
                                <i class="fas fa-calendar-times fa-3x mb-3 d-block"></i>
                                Belum ada jadwal yang diinputkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- PAGINATION --}}
            <div class="mt-3 d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    <span class="text-muted">Menampilkan {{ $jadwal->firstItem() }} - {{ $jadwal->lastItem() }} dari {{ $jadwal->total() }} Jadwal</span>
                </div>
                <div class="d-flex justify-content-end">
                    {{ $jadwal->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH JADWAL --}}
    <div class="modal fade" id="tambahJadwalModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle mr-2"></i>Tambah Jadwal Pelajaran
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('jadwal.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Kelas</strong></label>
                                    <select name="kelas_id" class="form-control" required>
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($kelas as $k)
                                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Hari</strong></label>
                                    <select name="hari" class="form-control" required>
                                        <option value="">-- Pilih Hari --</option>
                                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat'] as $hari)
                                            <option value="{{ $hari }}">{{ $hari }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Hari Sabtu tidak tersedia</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Jam Mulai</strong></label>
                                    <input type="time" name="jam_mulai" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Jam Selesai</strong></label>
                                    <input type="time" name="jam_selesai" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Mata Pelajaran</strong></label>
                                    <select name="id_mata_pelajaran" class="form-control" required>
                                        <option value="">-- Pilih Mapel --</option>
                                        @foreach($mapel as $m)
                                            <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Guru Pengajar</strong> <small class="text-muted">(Opsional)</small></label>
                                    <select name="guru_id" class="form-control">
                                        <option value="">-- Pilih Guru (Opsional) --</option>
                                        @foreach($guru as $g)
                                            <option value="{{ $g->id }}">{{ $g->user->name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Kosongkan jika belum ada guru</small>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 text-right">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>Simpan Jadwal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
