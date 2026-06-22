@extends('Layouts.LayoutsAdmin.app')

@section('title')
<title>Sistem Akademik - Input Kehadiran</title>
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0 text-gray-800">
        <i class="fas fa-clipboard-check" aria-hidden="true"></i> Input Kehadiran Siswa
    </h4>
    <a href="{{ route('MonitoringKehadiran') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Error!</strong> Terdapat kesalahan pada input:
    <ul class="mb-0 mt-2">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-edit"></i> Form Input Kehadiran
        </h6>
    </div>

    <div class="card-body">
        <!-- Filter Section -->
        <form method="GET" action="{{ route('kehadiran.store') }}" id="filterForm">
            <div class="row mb-4">
                <div class="col-md-3">
                    <label for="jurusan" class="form-label"><b>Jurusan <span class="text-danger">*</span></b></label>
                    <select name="jurusan" id="jurusan" class="form-control" required>
                        <option value="">-- Pilih Jurusan --</option>
                        @foreach($listJurusan as $j)
                            <option value="{{ $j }}" {{ $jurusan == $j ? 'selected' : '' }}>
                                {{ $j }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="kelas" class="form-label"><b>Kelas <span class="text-danger">*</span></b></label>
                    <select name="kelas" id="kelas" class="form-control" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($listKelas as $k)
                            <option value="{{ $k }}" {{ $kelas == $k ? 'selected' : '' }}>
                                {{ $k }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="tanggal" class="form-label"><b>Tanggal <span class="text-danger">*</span></b></label>
                    <input type="date" name="tanggal" id="tanggal"
                        class="form-control"
                        value="{{ $tanggal }}"
                        max="{{ date('Y-m-d') }}"
                        required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> Tampilkan Siswa
                    </button>
                </div>
            </div>
        </form>

        @if($jurusan && $kelas)
        <!-- Identitas Kelas -->
        <div class="alert alert-info">
            <div class="row">
                <div class="col-md-4">
                    <p class="mb-1"><b>Jurusan:</b> {{ $jurusan }}</p>
                </div>
                <div class="col-md-4">
                    <p class="mb-1"><b>Kelas:</b> {{ $kelas }}</p>
                </div>
                <div class="col-md-4">
                    <p class="mb-1"><b>Tanggal:</b> {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</p>
                </div>
            </div>
        </div>

        @if($siswaList->count() > 0)
        <!-- Form Input Kehadiran -->
        <form method="POST" action="{{ route('kehadiran.store') }}" id="formKehadiran">
            @csrf
            <input type="hidden" name="jurusan" value="{{ $jurusan }}">
            <input type="hidden" name="kelas" value="{{ $kelas }}">
            <input type="hidden" name="tanggal" value="{{ $tanggal }}">

            <div class="mb-3">
                <button type="button" class="btn btn-sm btn-success" onclick="setAllStatus('hadir')">
                    <i class="fas fa-check-double"></i> Semua Hadir
                </button>
                <button type="button" class="btn btn-sm btn-warning" onclick="setAllStatus('sakit')">
                    <i class="fas fa-notes-medical"></i> Semua Sakit
                </button>
                <button type="button" class="btn btn-sm btn-info" onclick="setAllStatus('izin')">
                    <i class="fas fa-envelope"></i> Semua Izin
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="setAllStatus('alpa')">
                    <i class="fas fa-times"></i> Semua Alpa
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="15%">NIS</th>
                            <th width="30%">Nama Siswa</th>
                            <th width="20%" class="text-center">Status Kehadiran</th>
                            <th width="30%">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswaList as $index => $siswa)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $siswa->nis }}</td>
                            <td>{{ $siswa->nama }}</td>
                            <td>
                                <input type="hidden" name="kehadiran[{{ $index }}][siswa_id]" value="{{ $siswa->id }}">
                                <div class="btn-group btn-group-sm d-flex" role="group">
                                    <input type="radio"
                                        class="btn-check"
                                        name="kehadiran[{{ $index }}][status]"
                                        id="hadir_{{ $siswa->id }}"
                                        value="hadir"
                                        {{ $siswa->kehadiran_status == 'hadir' ? 'checked' : '' }}
                                        required>
                                    <label class="btn btn-outline-success" for="hadir_{{ $siswa->id }}">
                                        <i class="fas fa-check"></i> Hadir
                                    </label>

                                    <input type="radio"
                                        class="btn-check"
                                        name="kehadiran[{{ $index }}][status]"
                                        id="sakit_{{ $siswa->id }}"
                                        value="sakit"
                                        {{ $siswa->kehadiran_status == 'sakit' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-warning" for="sakit_{{ $siswa->id }}">
                                        <i class="fas fa-notes-medical"></i> Sakit
                                    </label>

                                    <input type="radio"
                                        class="btn-check"
                                        name="kehadiran[{{ $index }}][status]"
                                        id="izin_{{ $siswa->id }}"
                                        value="izin"
                                        {{ $siswa->kehadiran_status == 'izin' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-info" for="izin_{{ $siswa->id }}">
                                        <i class="fas fa-envelope"></i> Izin
                                    </label>

                                    <input type="radio"
                                        class="btn-check"
                                        name="kehadiran[{{ $index }}][status]"
                                        id="alpa_{{ $siswa->id }}"
                                        value="alpa"
                                        {{ $siswa->kehadiran_status == 'alpa' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-danger" for="alpa_{{ $siswa->id }}">
                                        <i class="fas fa-times"></i> Alpa
                                    </label>
                                </div>
                            </td>
                            <td>
                                <input type="text"
                                    name="kehadiran[{{ $index }}][keterangan]"
                                    class="form-control form-control-sm"
                                    placeholder="Keterangan (opsional)"
                                    value="{{ $siswa->kehadiran_keterangan }}">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary float-right">
                        <i class="fas fa-save"></i> Simpan Kehadiran
                    </button>
                    <a href="{{ route('detail-kehadiran') }}" class="btn btn-secondary float-right mr-2">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </div>
        </form>
        @else
        <div class="alert alert-warning text-center">
            <i class="fas fa-exclamation-triangle"></i>
            Tidak ada data siswa untuk kelas ini
        </div>
        @endif

        @else
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle"></i>
            Silakan pilih Jurusan, Kelas, dan Tanggal untuk menampilkan form input kehadiran
        </div>
        @endif
    </div>
</div>

@endsection

@push('styles')
<style>
    .btn-check {
        position: absolute;
        clip: rect(0,0,0,0);
        pointer-events: none;
    }

    .btn-check:checked + label {
        color: #fff;
        border-color: transparent;
    }

    .btn-check:checked + .btn-outline-success {
        background-color: #28a745;
    }

    .btn-check:checked + .btn-outline-warning {
        background-color: #ffc107;
    }

    .btn-check:checked + .btn-outline-info {
        background-color: #17a2b8;
    }

    .btn-check:checked + .btn-outline-danger {
        background-color: #dc3545;
    }

    .btn-group label {
        cursor: pointer;
        margin-bottom: 0;
    }

    .table td {
        vertical-align: middle;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Auto submit on filter change
    $('#jurusan, #kelas, #tanggal').on('change', function() {
        $('#filterForm').submit();
    });

    // Confirm before submit
    $('#formKehadiran').on('submit', function(e) {
        if (!confirm('Apakah Anda yakin ingin menyimpan data kehadiran ini?')) {
            e.preventDefault();
            return false;
        }
    });
});

function setAllStatus(status) {
    $('input[type="radio"][value="' + status + '"]').prop('checked', true);

    // Update visual state
    $('input[type="radio"][value="' + status + '"]').each(function() {
        $(this).next('label').addClass('active');
    });
}
</script>
@endpush
