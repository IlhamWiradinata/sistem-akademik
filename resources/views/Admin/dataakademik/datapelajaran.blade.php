@extends('Layouts.LayoutsAdmin.app')

@section('title')
<title>Sistem Akademik - Kelola Mata Pelajaran</title>
@endsection

@section('content')
{{-- PAGE HEADING --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h4 class="h4 mb-0 text-gray-800">
        <i class="fas fa-book mr-2"></i> Kelola Mata Pelajaran
    </h4>
    <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambahMapel">
        <i class="fas fa-plus"></i> Tambah Mapel
    </button>
</div>

{{-- SESSION SUCCESS ALERT --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

{{-- Statistik Card --}}
<div class="row">
    <!-- Total Mata Pelajaran -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Mata Pelajaran
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalMapel }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-book fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mapel Normatif -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Normatif
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalNormatif }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-flag fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mapel Adaptif -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Adaptif
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAdaptif }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mapel Produktif -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Produktif
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProduktif }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tools fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- FILTER --}}
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('DataMapel') }}" class="form-row align-items-end">
            {{-- Search --}}
            <div class="form-group col-md-4 mb-0">
                <label class="font-weight-bold">Cari Mapel</label>
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Kode atau Nama Mapel" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Filter Kelompok --}}
            <div class="form-group col-md-3 mb-0">
                <label class="font-weight-bold">Filter Kelompok</label>
                <select name="kelompok" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Semua Kelompok --</option>
                    @foreach ($list_kelompok as $kelompok)
                        <option value="{{ $kelompok }}" {{ request('kelompok') == $kelompok ? 'selected' : '' }}>
                            {{ $kelompok }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Reset Filter --}}
            <div class="form-group col-md-2 mb-0">
                <label class="font-weight-bold d-block">&nbsp;</label>
                <a href="{{ route('DataMapel') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-sync-alt"></i> Reset
                </a>
            </div>

            <div class="form-group col-md-3 mb-0 text-right">
                <label class="font-weight-bold d-block">&nbsp;</label>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('mapel.export', ['kelompok'=>request('kelompok')]) }}" target="_blank" class="btn btn-danger btn-sm">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- TABEL MAPEL --}}
<div class="card shadow border-0 rounded">
    <div class="card-body table-responsive">
        {{-- HEADING SEBELUM TABEL --}}
        <div class="mb-3">
            <h5 class="font-weight-bold text-gray-700">
                Daftar Mata Pelajaran
                @if(request('kelompok'))
                    <span class="badge badge-info">Filter: {{ request('kelompok') }}</span>
                @endif
                @if(request('search'))
                    <span class="badge badge-secondary">Pencarian: "{{ request('search') }}"</span>
                @endif
            </h5>
        </div>

        <table class="table table-hover table-bordered">
            <thead class="thead-light text-center">
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">Kode Mapel</th>
                    <th width="40%">Nama Mapel</th>
                    <th width="15%">Kelompok</th>
                    <th width="8%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mapel as $index => $m)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $m->kode_mapel }}</strong></td>
                        <td>{{ $m->nama_mapel }}</td>
                        <td>
                            @if($m->kelompok == 'Produktif')
                                <span class="badge badge-warning">{{ $m->kelompok }}</span>
                            @elseif($m->kelompok == 'Adaptif')
                                <span class="badge badge-info">{{ $m->kelompok }}</span>
                            @elseif($m->kelompok == 'Normatif')
                                <span class="badge badge-success">{{ $m->kelompok }}</span>
                            @else
                                <span class="badge badge-secondary">{{ $m->kelompok }}</span>
                            @endif
                        </td>
                        <td>
                            {{-- EDIT --}}
                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEditMapel{{ $m->id }}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>

                            {{-- DELETE --}}
                            <form action="{{ route('mapel.delete', $m->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus mata pelajaran {{ $m->nama_mapel }}?')" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    {{-- MODAL EDIT --}}
                    <div class="modal fade" id="modalEditMapel{{ $m->id }}" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="{{ route('mapel.update', $m->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title">Edit Mata Pelajaran</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Kode Mapel <span class="text-danger">*</span></label>
                                            <input type="text" name="kode_mapel" class="form-control" value="{{ $m->kode_mapel }}" required maxlength="10">
                                            <small class="text-muted">Maksimal 10 karakter</small>
                                        </div>
                                        <div class="form-group">
                                            <label>Nama Mapel <span class="text-danger">*</span></label>
                                            <input type="text" name="nama_mapel" class="form-control" value="{{ $m->nama_mapel }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Kelompok <span class="text-danger">*</span></label>
                                            <select name="kelompok" class="form-control" required>
                                                @foreach($list_kelompok as $option)
                                                    <option value="{{ $option }}" {{ $m->kelompok == $option ? 'selected' : '' }}>
                                                        {{ $option }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fas fa-folder-open fa-3x mb-3 d-block"></i>
                            Belum ada mata pelajaran.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- PAGINATION --}}
        <div class="mt-3 d-flex justify-content-between align-items-center">
            <div class="text-muted">
                <span class="text-muted">Menampilkan {{ $mapel->firstItem() }} - {{ $mapel->lastItem() }} dari {{ $mapel->total() }} Mata Pelajaran</span>
            </div>
            <div class="d-flex justify-content-end">
                {{ $mapel->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambahMapel" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{ route('mapel.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Mata Pelajaran</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Kode Mapel <span class="text-danger">*</span></label>
                    <input type="text" name="kode_mapel" class="form-control" placeholder="Contoh: BI, MTK, INF" required maxlength="10">
                    <small class="text-muted">Gunakan kode singkat (maksimal 10 karakter)</small>
                </div>
                <div class="form-group">
                    <label>Nama Mapel <span class="text-danger">*</span></label>
                    <input type="text" name="nama_mapel" class="form-control" placeholder="Contoh: Bahasa Indonesia" required>
                </div>
                <div class="form-group">
                    <label>Kelompok <span class="text-danger">*</span></label>
                    <select name="kelompok" class="form-control" required>
                        <option value="">-- Pilih Kelompok --</option>
                        <option value="Normatif">Normatif</option>
                        <option value="Adaptif">Adaptif</option>
                        <option value="Produktif">Produktif</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
