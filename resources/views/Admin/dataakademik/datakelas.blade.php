@extends('Layouts.LayoutsAdmin.app')

@section('title')
<title> Sistem Akademik - Kelola Data Kelas </title>
@endsection

@section('content')
{{-- PAGE HEADING --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h4 class="h4 mb-0 text-gray-800">
        <i class="fas fa-school mr-2"></i> Kelola Data Kelas
    </h4>
    <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambahKelas">
        <i class="fas fa-plus"></i> Tambah Kelas
    </button>
</div>

{{-- STATISTIK CARD --}}
<div class="row">
    <!-- Total Kelas -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Kelas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalKelas }}</div>
                        <div class="text-muted small mt-1">Kelas aktif</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-school fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Siswa -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Siswa
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSiswa ?? $siswa->count() }}</div>
                        <div class="text-muted small mt-1">Siswa terdaftar</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-graduate fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Wali Kelas -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-{{ $totalKelasTanpaWali > 0 ? 'danger' : 'success' }} shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-{{ $totalKelasTanpaWali > 0 ? 'danger' : 'success' }} text-uppercase mb-1">
                            Status Wali Kelas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $totalKelasDenganWali }} <small class="text-muted">/ {{ $totalKelas }} kelas</small>
                        </div>
                        <div class="text-muted small mt-1">
                            @if($totalKelasTanpaWali > 0)
                                {{ $totalKelasTanpaWali }} kelas belum memiliki wali kelas
                            @else
                                Semua kelas sudah memiliki wali kelas
                            @endif
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chalkboard-teacher fa-2x text-{{ $totalKelasTanpaWali > 0 ? 'danger' : 'success' }}"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- FILTER + SEARCH --}}
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('DataKelas') }}" class="form-row align-items-end">
            {{-- Tahun Ajaran --}}
            <div class="form-group col-md-4">
                <label class="font-weight-bold">Tahun Ajaran</label>
                <select name="tahun_ajaran" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Semua Tahun --</option>
                    @foreach ($list_tahun as $tahun)
                        <option value="{{ $tahun }}" {{ request('tahun_ajaran') == $tahun ? 'selected' : '' }}>
                            {{ $tahun }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tingkat Kelas --}}
            <div class="form-group col-md-3">
                <label class="font-weight-bold">Tingkat Kelas</label>
                <select name="tingkat" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Semua Tingkat --</option>
                    @foreach (['X','XI','XII'] as $tingkat)
                        <option value="{{ $tingkat }}" {{ request('tingkat') == $tingkat ? 'selected' : '' }}>
                            {{ $tingkat }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Reset Filter --}}
            <div class="form-group col-md-2">
                <label class="font-weight-bold d-block">&nbsp;</label>
                <a href="{{ route('DataKelas') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-sync-alt"></i> Reset
                </a>
            </div>

            {{-- Export --}}
            <div class="form-group col-md-3 text-right">
                <label class="font-weight-bold d-block">&nbsp;</label>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('kelas.export', ['format'=>'pdf']) }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- TABEL DATA KELAS --}}
<div class="card shadow mb-3">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table"></i> Daftar Kelas
        </h6>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
            <thead class="thead-light">
                <tr class="text-center">
                    <th>No</th>
                    <th>Nama Kelas</th>
                    <th>Jurusan</th>
                    <th>Wali Kelas</th>
                    <th>Tahun Ajaran</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kelas as $k)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $k->nama_kelas }}</td>
                        <td>{{ $k->jurusan->nama_jurusan ?? '-' }}</td>
                        <td>{{ $k->waliKelas->user->name ?? '-' }}</td>
                        <td>{{ $k->tahun_ajaran }}</td>
                        <td>
                            {{-- BUTTON EDIT --}}
                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEditKelas{{ $k->id }}">
                                <i class="fas fa-edit"></i>
                            </button>

                            {{-- BUTTON DELETE --}}
                            <form action="{{ route('kelas.delete', $k->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus kelas ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>

                            {{-- QUICK VIEW --}}
                            <button class="btn btn-info btn-sm btn-quick-view" data-toggle="modal"  data-target="#modalQuickView" data-id="{{ $k->id }}">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>

                    {{-- MODAL EDIT --}}
                    <div class="modal fade" id="modalEditKelas{{ $k->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="{{ route('kelas.update', $k->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title">Edit Kelas</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Nama Kelas</label>
                                            <input type="text" class="form-control" name="nama_kelas" value="{{ $k->nama_kelas }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Jurusan</label>
                                            <select name="jurusan_id" class="form-control" required>
                                                @foreach ($jurusan as $j)
                                                    <option value="{{ $j->id }}" {{ $k->jurusan_id == $j->id ? 'selected' : '' }}>
                                                        {{ $j->nama_jurusan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Wali Kelas</label>
                                            <select name="wali_kelas_id" class="form-control">
                                                <option value="">-- Pilih --</option>
                                                @foreach ($guru as $g)
                                                    <option value="{{ $g->id }}" {{ $k->wali_kelas_id == $g->id ? 'selected' : '' }}>
                                                        {{ $g->user->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Tahun Ajaran</label>
                                            <input type="text" class="form-control" name="tahun_ajaran" value="{{ $k->tahun_ajaran }}" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada data kelas.</td>
                        </tr>
                @endforelse
            </tbody>
        </table>

        {{-- PAGINATION --}}
        <div class="mt-3 d-flex justify-content-between align-items-center">
            <div class="text-muted">
                <span class="text-muted">Menampilkan {{ $kelas->firstItem() }} - {{ $kelas->lastItem() }} dari {{ $kelas->total() }} Kelas</span>
            </div>
            <div class="d-flex justify-content-end">
                {{ $kelas->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<!-- QUICK VIEW MODAL -->
<div class="modal fade" id="modalQuickView" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Detail Kelas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body" id="quickViewContent">
                <p class="text-center">Loading...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambahKelas" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('kelas.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Kelas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Kelas</label>
                    <input type="text" class="form-control" name="nama_kelas" required>
                </div>

                <div class="form-group">
                    <label>Jurusan</label>
                    <select class="form-control" name="jurusan_id" required>
                        <option value="">-- Pilih Jurusan --</option>
                        @foreach ($jurusan as $j)
                            <option value="{{ $j->id }}">{{ $j->nama_jurusan }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Wali Kelas</label>
                    <select class="form-control" name="wali_kelas_id">
                        <option value="">-- Pilih Wali Kelas --</option>
                        @foreach ($guru as $g)
                            <option value="{{ $g->id }}">{{ $g->user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Tahun Ajaran</label>
                    <input type="text" class="form-control" name="tahun_ajaran" placeholder="2024/2025" required>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).on('click', '.btn-quick-view', function () {
    const kelasId = $(this).data('id');

    $('#quickViewContent').html('<p class="text-center">Loading...</p>');

    $.ajax({
        url: "{{ route('kelas.quickView', ':id') }}".replace(':id', kelasId),
        type: 'GET',
        success: function (res) {

            let html = `
                <h5>${res.kelas.nama_kelas} (${res.kelas.tahun_ajaran})</h5>
                <p><strong>Jurusan:</strong> ${res.kelas.jurusan?.nama_jurusan ?? '-'}</p>
                <hr>
                <h6>Daftar Siswa</h6>
            `;

            if (res.siswa.length === 0) {
                html += `<p class="text-muted">Belum ada siswa</p>`;
            } else {
                html += `
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>NIS</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                res.siswa.forEach((s, i) => {
                    html += `
                        <tr>
                            <td>${i + 1}</td>
                            <td>${s.user?.name ?? '-'}</td>
                            <td>${s.nis ?? '-'}</td>
                        </tr>
                    `;
                });

                html += `</tbody></table>`;
            }

            $('#quickViewContent').html(html);
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            $('#quickViewContent').html(
                '<p class="text-danger text-center">Gagal memuat data</p>'
            );
        }
    });
});
</script>

@endsection
