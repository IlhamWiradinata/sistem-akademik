@extends('layouts.layoutsadmin.app')

@section('title')
<title>Detail Kehadiran Siswa - {{ $siswa->user->name }}</title>
@endsection

@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-user-check"></i> Detail Kehadiran Siswa
    </h1>
    <div>
        <a href="{{ url()->previous() }}" class="btn btn-secondary btn-icon-split mr-2">
            <span class="icon text-white-50">
                <i class="fas fa-arrow-left"></i>
            </span>
            <span class="text">Kembali</span>
        </a>
        <button class="btn btn-primary btn-icon-split" data-toggle="modal" data-target="#tambahKehadiranModal">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Tambah Kehadiran</span>
        </button>
    </div>
</div>

<!-- Alert Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<!-- Student Profile Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-user-graduate"></i> Profil Siswa
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="text-xs text-muted mb-1">NIS</div>
                <div class="h6 font-weight-bold text-dark">
                    <i class="fas fa-id-card text-primary mr-2"></i>
                    {{ $siswa->nis }}
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="text-xs text-muted mb-1">Nama Siswa</div>
                <div class="h6 font-weight-bold text-dark">
                    <i class="fas fa-user text-primary mr-2"></i>
                    {{ $siswa->user->name }}
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="text-xs text-muted mb-1">Kelas</div>
                <div class="h6 font-weight-bold text-dark">
                    <i class="fas fa-school text-primary mr-2"></i>
                    {{ $siswa->kelas->nama_kelas }}
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="text-xs text-muted mb-1">Jurusan</div>
                <div class="h6 font-weight-bold text-dark">
                    <i class="fas fa-graduation-cap text-primary mr-2"></i>
                    {{ $siswa->jurusan->nama_jurusan ?? '-' }}
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $hadir = $kehadiran->where('status', 'hadir')->count();
    $izin = $kehadiran->where('status', 'izin')->count();
    $sakit = $kehadiran->where('status', 'sakit')->count();
    $alpa = $kehadiran->where('status', 'alpa')->count();
    $total = $hadir + $izin + $sakit + $alpa;
    $persentaseHadir = $total > 0 ? round(($hadir / $total) * 100, 1) : 0;
@endphp

<!-- Statistics Cards -->
<div class="row">
    <!-- Total Kehadiran -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Kehadiran
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total }}</div>
                        <div class="text-xs text-muted mt-1">
                            Catatan kehadiran
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Persentase Kehadiran -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Kehadiran
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $persentaseHadir }}%</div>
                        <div class="text-xs text-muted mt-1">
                            {{ $hadir }} dari {{ $total }} hari
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Kehadiran Terbanyak -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        @php
                            $statusData = [
                                'Hadir' => $hadir,
                                'Izin' => $izin,
                                'Sakit' => $sakit,
                                'Alpa' => $alpa
                            ];
                            arsort($statusData);
                            $statusTerbanyak = key($statusData);
                        @endphp
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Status Terbanyak
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statusTerbanyak }}</div>
                        <div class="text-xs text-muted mt-1">
                            {{ $statusData[$statusTerbanyak] }} kali
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Terakhir Absen -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Terakhir Absen
                        </div>
                        @if($kehadiran->count() > 0)
                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                            {{ \Carbon\Carbon::parse($kehadiran->first()->tanggal)->translatedFormat('d M Y') }}
                        </div>
                        <div class="text-xs text-muted mt-1">
                            Status:
                            <span class="badge badge-{{ $kehadiran->first()->status == 'hadir' ? 'success' : ($kehadiran->first()->status == 'izin' ? 'info' : ($kehadiran->first()->status == 'sakit' ? 'warning' : 'danger')) }}">
                                {{ ucfirst($kehadiran->first()->status) }}
                            </span>
                        </div>
                        @else
                        <div class="h6 mb-0 font-weight-bold text-gray-800">-</div>
                        <div class="text-xs text-muted mt-1">Belum ada data</div>
                        @endif
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-history fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Status Kehadiran -->
<div class="row mb-4">
    <div class="col-xl-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie"></i> Detail Status Kehadiran
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach([
                        ['label' => 'Hadir', 'count' => $hadir, 'color' => 'success', 'icon' => 'check-circle'],
                        ['label' => 'Izin', 'count' => $izin, 'color' => 'info', 'icon' => 'file-alt'],
                        ['label' => 'Sakit', 'count' => $sakit, 'color' => 'warning', 'icon' => 'notes-medical'],
                        ['label' => 'Alpa', 'count' => $alpa, 'color' => 'danger', 'icon' => 'times-circle']
                    ] as $status)
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-{{ $status['color'] }} h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="text-xs font-weight-bold text-{{ $status['color'] }} text-uppercase mb-1">
                                            {{ $status['label'] }}
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold">{{ $status['count'] }}</div>
                                        <div class="text-xs text-muted">
                                            @if($total > 0)
                                                {{ round(($status['count'] / $total) * 100, 1) }}% dari total
                                            @else
                                                0% dari total
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <i class="fas fa-{{ $status['icon'] }} fa-2x text-{{ $status['color'] }}"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Progress Bar Visual -->
                @if($total > 0)
                <div class="mt-3">
                    <h6 class="small font-weight-bold mb-2">Distribusi Kehadiran:</h6>
                    <div class="progress mb-2" style="height: 20px;">
                        <div class="progress-bar bg-success" role="progressbar"
                             style="width: {{ ($hadir/$total)*100 }}%"
                             title="Hadir: {{ $hadir }} ({{ round(($hadir/$total)*100, 1) }}%)">
                        </div>
                        <div class="progress-bar bg-info" role="progressbar"
                             style="width: {{ ($izin/$total)*100 }}%"
                             title="Izin: {{ $izin }} ({{ round(($izin/$total)*100, 1) }}%)">
                        </div>
                        <div class="progress-bar bg-warning" role="progressbar"
                             style="width: {{ ($sakit/$total)*100 }}%"
                             title="Sakit: {{ $sakit }} ({{ round(($sakit/$total)*100, 1) }}%)">
                        </div>
                        <div class="progress-bar bg-danger" role="progressbar"
                             style="width: {{ ($alpa/$total)*100 }}%"
                             title="Alpa: {{ $alpa }} ({{ round(($alpa/$total)*100, 1) }}%)">
                        </div>
                    </div>
                    <div class="text-center small">
                        <span class="mr-3"><i class="fas fa-square text-success"></i> Hadir</span>
                        <span class="mr-3"><i class="fas fa-square text-info"></i> Izin</span>
                        <span class="mr-3"><i class="fas fa-square text-warning"></i> Sakit</span>
                        <span class="mr-3"><i class="fas fa-square text-danger"></i> Alpa</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Table Kehadiran -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table"></i> Riwayat Kehadiran
        </h6>
        <div>
            <span class="badge badge-primary">
                <i class="fas fa-list"></i> {{ $kehadiran->total() }} Data
            </span>
        </div>
    </div>
    <div class="card-body">
        <!-- Filter Bulan -->
        <div class="row mb-4">
            <div class="col-md-4">
                <label class="small font-weight-bold">Filter Bulan:</label>
                <select class="form-control form-control-sm" id="filterBulan">
                    <option value="">Semua Bulan</option>
                    @foreach([
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                    ] as $key => $bulan)
                    <option value="{{ $key }}">{{ $bulan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="small font-weight-bold">Filter Status:</label>
                <select class="form-control form-control-sm" id="filterStatus">
                    <option value="">Semua Status</option>
                    <option value="hadir">Hadir</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                    <option value="alpa">Alpa</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="small font-weight-bold">Cari:</label>
                <input type="text" class="form-control form-control-sm" id="searchKehadiran"
                       placeholder="Cari berdasarkan keterangan...">
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="tabelKehadiran" width="100%" cellspacing="0">
                <thead class="bg-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Tanggal</th>
                        <th width="12%">Hari</th>
                        <th width="15%">Status</th>
                        <th>Keterangan</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kehadiran as $index => $k)
                    @php
                        $tanggal = \Carbon\Carbon::parse($k->tanggal);
                    @endphp
                    <tr>
                        <td class="text-center align-middle">{{ ($kehadiran->currentPage() - 1) * $kehadiran->perPage() + $index + 1 }}</td>
                        <td class="align-middle">
                            <div class="font-weight-bold">{{ $tanggal->translatedFormat('d M Y') }}</div>
                            <div class="small text-muted">{{ $tanggal->format('m/Y') }}</div>
                        </td>
                        <td class="align-middle text-center">
                            <span class="badge badge-{{ in_array($tanggal->dayOfWeek, [0, 6]) ? 'secondary' : 'primary' }}">
                                {{ $tanggal->translatedFormat('l') }}
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            @php
                                $badgeClass = [
                                    'hadir' => 'success',
                                    'izin' => 'info',
                                    'sakit' => 'warning',
                                    'alpa' => 'danger'
                                ][$k->status];
                            @endphp
                            <span class="badge badge-{{ $badgeClass }} px-3 py-2">
                                <i class="fas fa-{{ $k->status == 'hadir' ? 'check' : ($k->status == 'izin' ? 'file-alt' : ($k->status == 'sakit' ? 'notes-medical' : 'times')) }} mr-1"></i>
                                {{ ucfirst($k->status) }}
                            </span>
                        </td>
                        <td class="align-middle">
                            @if($k->keterangan)
                                {{ $k->keterangan }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            <div class="btn-group" role="group">
                                <button class="btn btn-warning btn-sm md-3"
                                        onclick="editKehadiran({{ $k->id }}, '{{ $k->tanggal }}', '{{ $k->status }}', `{{ $k->keterangan ?? '' }}`)"
                                        title="Edit Kehadiran" data-toggle="tooltip">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm delete-btn"
                                        onclick="confirmDelete({{ $k->id }})"
                                        title="Hapus Kehadiran" data-toggle="tooltip">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fas fa-calendar-times fa-3x mb-3 text-gray-300"></i>
                            <h6 class="text-gray-600">Belum ada data kehadiran</h6>
                            <p class="small text-muted mb-0">Klik tombol "Tambah Kehadiran" untuk menambah data</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
</div>

<!-- Modal Tambah Kehadiran -->
<div class="modal fade" id="tambahKehadiranModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle"></i> Tambah Kehadiran
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('kehadiran.store') }}" method="POST">
                @csrf
                <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">

                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control"
                               value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="hadir">Hadir</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                            <option value="alpa">Alpa</option>
                        </select>
                    </div>

                    <input type="hidden" name="semester" value="{{ $siswa->kelasSiswa->last()->semester ?? '' }}">
                    <input type="hidden" name="tahun_ajaran" value="{{ $siswa->kelasSiswa->last()->tahun_ajaran ?? '' }}">

                    <div class="form-group">
                        <label class="font-weight-bold">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3"
                                  placeholder="Masukkan keterangan (opsional)"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Kehadiran -->
<div class="modal fade" id="editKehadiranModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">
                    <i class="fas fa-edit"></i> Edit Kehadiran
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editKehadiranForm" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" id="edit_tanggal" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Status <span class="text-danger">*</span></label>
                        <select name="status" id="edit_status" class="form-control" required>
                            <option value="hadir">Hadir</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                            <option value="alpa">Alpa</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Keterangan</label>
                        <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save mr-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data kehadiran ini?</p>
                <p class="text-danger font-weight-bold">Aksi ini tidak dapat dibatalkan!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .progress-bar {
        cursor: help;
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }

    .badge {
        font-size: 0.85em;
        font-weight: 500;
    }

    .btn-group .btn {
        border-radius: 4px;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Tooltip
    $('[data-toggle="tooltip"]').tooltip();

    // Filter functionality
    $('#filterBulan, #filterStatus').on('change', function() {
        filterTable();
    });

    $('#searchKehadiran').on('keyup', function() {
        filterTable();
    });

    function filterTable() {
        var bulan = $('#filterBulan').val().toLowerCase();
        var status = $('#filterStatus').val().toLowerCase();
        var search = $('#searchKehadiran').val().toLowerCase();

        $('#tabelKehadiran tbody tr').each(function() {
            var row = $(this);
            var rowBulan = row.find('td:nth-child(2) .small').text().split('/')[0];
            var rowStatus = row.find('td:nth-child(4) .badge').text().toLowerCase();
            var rowKeterangan = row.find('td:nth-child(5)').text().toLowerCase();

            var show = true;

            if (bulan && rowBulan !== bulan) {
                show = false;
            }
            if (status && !rowStatus.includes(status)) {
                show = false;
            }
            if (search && !rowKeterangan.includes(search)) {
                show = false;
            }

            row.toggle(show);
        });
    }

    // Auto-submit delete form
    window.confirmDelete = function(id) {
        // PERBAIKAN: Sesuaikan dengan route di web.php
        var deleteUrl = "{{ route('kehadiran.delete', ':id') }}".replace(':id', id);
        $('#deleteForm').attr('action', deleteUrl);
        $('#deleteModal').modal('show');
    };
});

// Edit kehadiran function
function editKehadiran(id, tanggal, status, keterangan) {
    // Format tanggal untuk input
    var formattedDate = new Date(tanggal).toISOString().split('T')[0];

    document.getElementById('edit_tanggal').value = formattedDate;
    document.getElementById('edit_status').value = status;
    document.getElementById('edit_keterangan').value = keterangan ? keterangan : '';

    // PERBAIKAN: Sesuaikan dengan route di web.php
    var updateUrl = "{{ route('kehadiran.update', ':id') }}".replace(':id', id);
    document.getElementById('editKehadiranForm').action = updateUrl;

    $('#editKehadiranModal').modal('show');
}
</script>
@endpush

<style>
    .pagination {
    margin-bottom: 0;
}

.page-item.active .page-link {
    background-color: #4e73df;
    border-color: #4e73df;
}

.page-link {
    color: #4e73df;
}

.page-link:hover {
    color: #224abe;
}
</style>
