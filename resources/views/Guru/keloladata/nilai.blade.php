@extends('layouts.layoutsguru.app')

@section('title')
<title>Sistem Akademik - Kelola Nilai</title>
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-graduation-cap"></i> Kelola Nilai Siswa
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
            <i class="fas fa-filter"></i> Filter Kelas & Mata Pelajaran
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('DataNilai') }}" id="filterForm">
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

                <!-- Mata Pelajaran -->
                <div class="col-md-3 mb-3">
                    <label class="font-weight-bold small mb-2">
                        <i class="fas fa-book"></i> Mata Pelajaran
                    </label>
                    <select name="mapel_id" id="mapel_id" class="form-control">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($listMapel as $mapel)
                            <option value="{{ $mapel->id }}" {{ $mapelId == $mapel->id ? 'selected' : '' }}>
                                {{ $mapel->nama_mapel }}
                            </option>
                        @endforeach
                    </select>
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
@if($kelasId && $semester && $mapelId)
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i>
    <strong>Filter Aktif:</strong>
    Tahun Ajaran <strong>{{ $tahunAjaran }}</strong> |
    Semester <strong>{{ $semester }}</strong> |
    Kelas <strong>{{ $infoCard['kelas_nama'] ?? '-' }}</strong> |
    Mapel <strong>{{ $infoCard['mapel_nama'] ?? '-' }}</strong>
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

    <!-- Sudah Dinilai -->
    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Sudah Dinilai
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $infoCard['sudah_dinilai'] ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Belum Dinilai -->
    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Belum Dinilai
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $infoCard['belum_dinilai'] ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Persentase Dinilai -->
    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Persentase Dinilai
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $infoCard['persentase_dinilai'] ?? 0 }}%
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rata-rata Kelas -->
    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-secondary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                            Rata-rata Kelas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($infoCard['rata_rata_kelas'] ?? 0, 2) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Progress -->
    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-danger text-uppercase mb-2">
                    Status Penilaian
                </div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-success" role="progressbar"
                         style="width: {{ $infoCard['persentase_dinilai'] ?? 0 }}%">
                    </div>
                </div>
                <div class="mt-2 small text-muted text-center">
                    {{ $infoCard['persentase_dinilai'] ?? 0 }}% selesai
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Table Card -->
@if($kelasId && $mapelId && $siswaData->count() > 0)
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table"></i> Daftar Nilai Siswa
        </h6>
        <span class="badge badge-primary">{{ $siswaData->count() }} Siswa</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-primary text-white">
                    <tr class="text-center">
                        <th width="5%">No</th>
                        <th width="12%">NIS</th>
                        <th class="text-left" width="25%">Nama Siswa</th>
                        <th width="8%">Status</th>
                        <th width="8%">Tugas</th>
                        <th width="8%">Praktikum</th>
                        <th width="8%">UTS</th>
                        <th width="8%">UAS</th>
                        <th width="8%">Sikap</th>
                        <th width="10%">Rata-rata</th>
                        <th width="8%">Grade</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siswaData as $index => $siswa)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $siswa['nis'] }}</td>
                        <td><strong>{{ $siswa['nama'] }}</strong></td>
                        <td class="text-center">
                            <span class="badge badge-{{ $siswa['status_nilai'] == 'Sudah' ? 'success' : 'warning' }}">
                                {{ $siswa['status_nilai'] }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light">{{ $siswa['nilai_tugas'] }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light">{{ $siswa['nilai_praktikum'] }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light">{{ $siswa['nilai_uts'] }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light">{{ $siswa['nilai_uas'] }}</span>
                        </td>
                        <td class="text-center">
                            @if($siswa['sikap'] != '-')
                                <span class="badge badge-{{ $siswa['sikap'] == 'A' ? 'success' : ($siswa['sikap'] == 'B' ? 'info' : 'warning') }}">
                                    {{ $siswa['sikap'] }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <strong class="text-{{ $siswa['rata_rata'] >= 85 ? 'success' : ($siswa['rata_rata'] >= 75 ? 'info' : ($siswa['rata_rata'] > 0 ? 'warning' : 'muted')) }}">
                                {{ $siswa['rata_rata'] > 0 ? number_format($siswa['rata_rata'], 2) : '-' }}
                            </strong>
                        </td>
                        <td class="text-center">
                            @if($siswa['grade'] != '-')
                                <span class="badge badge-{{ $siswa['grade'] == 'A' ? 'success' : ($siswa['grade'] == 'B' ? 'info' : ($siswa['grade'] == 'C' ? 'warning' : 'danger')) }} px-3 py-2">
                                    {{ $siswa['grade'] }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-{{ $siswa['nilai_id'] ? 'warning' : 'success' }}"
                                    onclick="openModalNilai({{ json_encode($siswa) }})"
                                    title="{{ $siswa['nilai_id'] ? 'Edit Nilai' : 'Input Nilai' }}">
                                <i class="fas fa-{{ $siswa['nilai_id'] ? 'edit' : 'plus' }}"></i>
                                {{ $siswa['nilai_id'] ? 'Edit' : 'Input' }}
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@elseif($kelasId && $mapelId)
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
        <h5 class="text-gray-600">Silakan pilih Tahun Ajaran, Semester, Kelas, dan Mata Pelajaran</h5>
        <p class="text-muted">untuk menampilkan daftar siswa dan input nilai</p>
    </div>
</div>
@endif

<!-- Modal Input/Edit Nilai -->
<div class="modal fade" id="nilaiModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-edit"></i> <span id="modalTitle">Input Nilai</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('storeNilai') }}" method="POST">
                @csrf
                <input type="hidden" name="nis" id="modal_nisn">
                <input type="hidden" name="nip" value="{{ $guru->nip }}">
                <input type="hidden" name="id_mata_pelajaran" value="{{ $mapelId }}">
                <input type="hidden" name="semester" value="{{ $semester }}">
                <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran }}">

                <div class="modal-body">
                    <!-- Student Info -->
                    <div class="alert alert-light border">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Tahun Ajaran:</strong><br>
                                <span>{{ $tahunAjaran }}</span>
                            </div>
                            <div class="col-md-4">
                                <strong>Semester:</strong><br>
                                <span>{{ $semester }}</span>
                            </div>
                            <div class="col-md-4">
                                <strong>Mata Pelajaran:</strong><br>
                                <span>{{ $infoCard['mapel_nama'] ?? '-' }}</span>
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

                    <!-- Form Nilai -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Nilai Tugas <span class="text-muted">(0-100)</span></label>
                            <input type="number" name="nilai_tugas" id="modal_tugas" class="form-control" min="0" max="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Nilai Praktikum <span class="text-muted">(0-100)</span></label>
                            <input type="number" name="nilai_praktikum" id="modal_praktikum" class="form-control" min="0" max="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Nilai UTS <span class="text-muted">(0-100)</span></label>
                            <input type="number" name="nilai_uts" id="modal_uts" class="form-control" min="0" max="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Nilai UAS <span class="text-muted">(0-100)</span></label>
                            <input type="number" name="nilai_uas" id="modal_uas" class="form-control" min="0" max="100">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="font-weight-bold">Sikap <span class="text-danger">*</span></label>
                            <select name="sikap" id="modal_sikap" class="form-control" required>
                                <option value="">-- Pilih Sikap --</option>
                                <option value="A">A (Sangat Baik)</option>
                                <option value="B">B (Baik)</option>
                                <option value="C">C (Cukup)</option>
                                <option value="D">D (Kurang)</option>
                                <option value="E">E (Sangat Kurang)</option>
                            </select>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Catatan:</strong> Rata-rata dan Grade akan dihitung otomatis oleh sistem
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Nilai
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openModalNilai(siswa) {
    // Set title
    $('#modalTitle').text(siswa.nilai_id ? 'Edit Nilai - ' + siswa.nama : 'Input Nilai - ' + siswa.nama);

    // Isi form
    $('#modal_nisn').val(siswa.nis);
    $('#modal_nisn_display').text(siswa.nis);
    $('#modal_nama_display').text(siswa.nama);

    if (siswa.nilai_id) {
        $('#modal_tugas').val(siswa.nilai_tugas !== '-' ? siswa.nilai_tugas : '');
        $('#modal_praktikum').val(siswa.nilai_praktikum !== '-' ? siswa.nilai_praktikum : '');
        $('#modal_uts').val(siswa.nilai_uts !== '-' ? siswa.nilai_uts : '');
        $('#modal_uas').val(siswa.nilai_uas !== '-' ? siswa.nilai_uas : '');
        $('#modal_sikap').val(siswa.sikap !== '-' ? siswa.sikap : '');
    } else {
        $('#modal_tugas').val('');
        $('#modal_praktikum').val('');
        $('#modal_uts').val('');
        $('#modal_uas').val('');
        $('#modal_sikap').val('');
    }

    $('#nilaiModal').modal('show');
}

// Auto submit on filter change
$('#kelas_id, #semester, #mapel_id, select[name="tahun_ajaran"]').on('change', function() {
    // Auto submit jika semua field terisi
    if ($('#kelas_id').val() && $('#semester').val() && $('#mapel_id').val() && $('select[name="tahun_ajaran"]').val()) {
        $('#filterForm').submit();
    }
});
</script>
@endpush
