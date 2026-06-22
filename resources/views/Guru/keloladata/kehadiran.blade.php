@extends('Layouts.LayoutsGuru.app')

@section('title')
<title>Sistem Akademik - Kelola Presensi</title>
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-clipboard-check"></i> Kelola Presensi Siswa
    </h1>

</div>

<!-- Alert Messages -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle"></i> <strong>Ada kesalahan:</strong>
    <ul class="mb-0 mt-2">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
@endif

<!-- Identitas Guru Card -->
<div class="card border-left-primary shadow mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-10">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">NIP</div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $guru->nip }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Nama Guru</div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $guru->user->name }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Email</div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $guru->user->email ?? '-' }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 text-center">
                <i class="fas fa-chalkboard-teacher fa-3x text-primary" style="opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-filter"></i> Filter Kelas & Tanggal
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('DataKehadiran') }}" id="filterForm">
            <div class="row">
                <!-- Tahun Ajaran -->
                <div class="col-md-3 mb-3">
                    <label class="font-weight-bold small mb-2">
                        <i class="fas fa-calendar"></i> Tahun Ajaran
                    </label>
                    <select name="tahun_ajaran" class="form-control">
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        @foreach($listTahunAjaran as $ta)
                            <option value="{{ $ta }}" {{ $tahunAjaran == $ta ? 'selected' : '' }}>
                                {{ $ta }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Semester -->
                <div class="col-md-3 mb-3">
                    <label class="font-weight-bold small mb-2">
                        <i class="fas fa-calendar-alt"></i> Semester
                    </label>
                    <select name="semester" class="form-control">
                        <option value="Ganjil" {{ $semester == 'Ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
                        <option value="Genap" {{ $semester == 'Genap' ? 'selected' : '' }}>Semester Genap</option>
                    </select>
                </div>

                <!-- Kelas -->
                <div class="col-md-3 mb-3">
                    <label class="font-weight-bold small mb-2">
                        <i class="fas fa-school"></i> Kelas
                    </label>
                    <select name="kelas_id" id="kelas_id" class="form-control">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($listKelas as $kelas)
                            <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tanggal -->
                <div class="col-md-3 mb-3">
                    <label class="font-weight-bold small mb-2">
                        <i class="fas fa-calendar"></i> Tanggal
                    </label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control"
                           value="{{ $tanggal }}" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Tampilkan Data
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Info Alert -->
@if($kelasId && $semester && $tanggal)
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i>
    <strong>Filter Aktif:</strong>
    Tahun Ajaran <strong>{{ $tahunAjaran }}</strong> |
    Semester <strong>{{ $semester }}</strong> |
    Kelas <strong>{{ $infoCard['kelas_nama'] ?? '-' }}</strong> |
    Tanggal <strong>{{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}</strong>
</div>

<!-- Info Card -->
<div class="row mb-4">
    <!-- Total Siswa -->
    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Siswa
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $infoCard['total_siswa'] ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hadir -->
    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Hadir
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $infoCard['hadir'] ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Izin -->
    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Izin
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $infoCard['izin'] ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sakit -->
    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Sakit
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $infoCard['sakit'] ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hospital fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpa -->
    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Alpa
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $infoCard['alpa'] ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Persentase Hadir -->
    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-secondary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                            Persentase Hadir
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $infoCard['persentase_hadir'] ?? 0 }}%
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Table Card -->
@if($kelasId && $tanggal && $siswaData->count() > 0)
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table"></i> Daftar Kehadiran Siswa
            <small class="text-muted ml-2">
                {{ $infoCard['hari'] ?? '' }}, {{ $infoCard['tanggal_formatted'] ?? '' }}
            </small>
        </h6>
        <span class="badge badge-primary">{{ $siswaData->count() }} Siswa</span>
    </div>
    <div class="card-body p-0">
        <form action="{{ route('storeKehadiranHarian') }}" method="POST" id="formKehadiran">
            @csrf
            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
            <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
            <input type="hidden" name="semester" value="{{ $semester }}">
            <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran }}">

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-primary text-white">
                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th width="12%">NIS</th>
                            <th class="text-left" width="30%">Nama Siswa</th>
                            <th width="20%">Status</th>
                            <th width="25%">Keterangan</th>
                            <th width="8%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswaData as $index => $siswa)
                        <tr class="siswa-row-{{ $siswa['id'] }}">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $siswa['nis'] }}</td>
                            <td><strong>{{ $siswa['nama'] }}</strong></td>
                            <td class="text-center">
                                <span class="badge badge-{{
                                    $siswa['status'] == 'hadir' ? 'success' :
                                    ($siswa['status'] == 'izin' ? 'info' :
                                    ($siswa['status'] == 'sakit' ? 'warning' : 'danger'))
                                }} status-badge-{{ $siswa['id'] }}">
                                    {{ ucfirst($siswa['status']) }}
                                </span>
                                <!-- Hidden input untuk menyimpan status -->
                                <input type="hidden" name="kehadiran[{{ $index }}][siswa_id]" value="{{ $siswa['id'] }}">
                                <input type="hidden" name="kehadiran[{{ $index }}][status]" class="status-input-{{ $siswa['id'] }}" value="{{ $siswa['status'] }}">
                                <input type="hidden" name="kehadiran[{{ $index }}][keterangan]" class="keterangan-input-{{ $siswa['id'] }}" value="{{ $siswa['keterangan'] }}">
                            </td>
                            <td class="keterangan-display-{{ $siswa['id'] }}">
                                {{ $siswa['keterangan'] ?? '-' }}
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-warning"
                                        onclick="openModalKehadiran({{ json_encode($siswa) }})"
                                        title="Edit Kehadiran">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-light py-3 d-flex justify-content-between">
                <button class="btn btn-success" onclick="markAllStatus('hadir')">
                    <i class="fas fa-check-circle"></i> Tandai Semua Hadir
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Kehadiran
                </button>
            </div>
        </form>
    </div>
</div>
@elseif($kelasId && $tanggal)
<div class="card shadow mb-4">
    <div class="card-body text-center py-5">
        <i class="fas fa-inbox fa-2x text-gray-300 mb-3"></i>
        <p class="text-muted">Tidak ada data siswa untuk kelas dan semester ini</p>
    </div>
</div>
@else
<div class="card shadow mb-4">
    <div class="card-body text-center py-5">
        <i class="fas fa-filter fa-3x text-gray-300 mb-3"></i>
        <h5 class="text-gray-600">Silakan pilih Tahun Ajaran, Semester, Kelas, dan Tanggal</h5>
        <p class="text-muted">untuk menampilkan daftar siswa dan input kehadiran</p>
    </div>
</div>
@endif

<!-- Modal Edit Kehadiran -->
<div class="modal fade" id="kehadiranModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-edit"></i> <span id="modalTitle">Edit Kehadiran</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- Student Info -->
                <div class="alert alert-light border">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Tanggal:</strong><br>
                            <span>{{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Hari:</strong><br>
                            <span>{{ $infoCard['hari'] ?? '' }}</span>
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>NIS:</strong> <span id="modal_nisn_display"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Nama:</strong> <span id="modal_nama_display"></span>
                        </div>
                    </div>
                </div>

                <!-- Form Kehadiran -->
                <div class="form-group">
                    <label class="font-weight-bold">Status Kehadiran <span class="text-danger">*</span></label>
                    <select id="modal_status" class="form-control" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="hadir">✓ Hadir</option>
                        <option value="izin">✏ Izin</option>
                        <option value="sakit">🏥 Sakit</option>
                        <option value="alpa">✕ Alpa</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Keterangan (Opsional)</label>
                    <textarea id="modal_keterangan" class="form-control" rows="3"
                              placeholder="Contoh: Sakit demam, Izin acara keluarga, dll"></textarea>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Catatan:</strong> Data akan diperbarui di tabel dan tersimpan saat Anda klik tombol "Simpan Kehadiran"
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="button" class="btn btn-primary" onclick="saveKehadiran()">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentSiswa = null;

function openModalKehadiran(siswa) {
    currentSiswa = siswa;

    $('#modal_nisn_display').text(siswa.nis);
    $('#modal_nama_display').text(siswa.nama);
    $('#modal_status').val(siswa.status);
    $('#modal_keterangan').val(siswa.keterangan ?? '');
    $('#modalTitle').text('Edit Kehadiran - ' + siswa.nama);

    $('#kehadiranModal').modal('show');
}

function saveKehadiran() {
    const status = $('#modal_status').val();
    const keterangan = $('#modal_keterangan').val();

    if (!status) {
        alert('Silakan pilih status kehadiran');
        return;
    }

    if (!currentSiswa) {
        alert('Data siswa tidak ditemukan');
        return;
    }

    // Update hidden input
    $('.status-input-' + currentSiswa.id).val(status);
    $('.keterangan-input-' + currentSiswa.id).val(keterangan);

    // Update badge status di tabel
    const badgeClass = status == 'hadir' ? 'success' :
                      (status == 'izin' ? 'info' :
                      (status == 'sakit' ? 'warning' : 'danger'));

    const statusText = status.charAt(0).toUpperCase() + status.slice(1);

    $('.status-badge-' + currentSiswa.id)
        .removeClass('badge-success badge-info badge-warning badge-danger')
        .addClass('badge-' + badgeClass)
        .text(statusText);

    // Update keterangan di tabel
    $('.keterangan-display-' + currentSiswa.id).text(keterangan || '-');

    $('#kehadiranModal').modal('hide');
}

function markAllStatus(status) {
    let confirmMessage = '';
    let confirmText = '';

    switch(status) {
        case 'hadir':
            confirmMessage = 'Tandai semua siswa sebagai Hadir?';
            confirmText = 'hadir';
            break;
    }

    if (confirm(confirmMessage)) {
        // Update semua input dan badge
        @if($siswaData->count() > 0)
            const siswaData = {!! json_encode($siswaData) !!};

            siswaData.forEach((siswa, index) => {
                $('.status-input-' + siswa.id).val(status);
                $('.keterangan-input-' + siswa.id).val('');

                const badgeClass = status == 'hadir' ? 'success' :
                                  (status == 'izin' ? 'info' :
                                  (status == 'sakit' ? 'warning' : 'danger'));

                const statusText = status.charAt(0).toUpperCase() + status.slice(1);

                $('.status-badge-' + siswa.id)
                    .removeClass('badge-success badge-info badge-warning badge-danger')
                    .addClass('badge-' + badgeClass)
                    .text(statusText);

                $('.keterangan-display-' + siswa.id).text('-');
            });
        @endif
    }
}

function resetAllStatus() {
    if (confirm('Reset semua status kehadiran ke data awal?')) {
        location.reload();
    }
}

// Auto submit on filter change
$('#kelas_id, #semester, select[name="tahun_ajaran"], #tanggal').on('change', function() {
    // Auto submit jika semua field terisi
    if ($('#kelas_id').val() && $('#semester').val() && $('select[name="tahun_ajaran"]').val() && $('#tanggal').val()) {
        $('#filterForm').submit();
    }
});
</script>
@endpush
