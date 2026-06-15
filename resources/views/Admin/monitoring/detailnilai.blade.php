@extends('layouts.layoutsadmin.app')

@section('title')
<title>Detail Nilai Siswa - Sistem Akademik</title>
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-graduation-cap"></i> Detail Nilai Siswa
    </h1>
    <div>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <button class="btn btn-primary" data-toggle="modal" data-target="#tambahNilaiModal">
            <i class="fas fa-plus"></i> Tambah Nilai
        </button>
    </div>
</div>

<!-- Alert Messages -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
@endif

<!-- Student Profile Card -->
<div class="card border-left-primary shadow mb-4">
    <div class="card-body py-3">
        <div class="row">
            <div class="col-md-3 mb-2">
                <div class="text-xs text-muted mb-1">
                    <i class="fas fa-id-card text-primary"></i> NIS
                </div>
                <div class="h6 mb-0 font-weight-bold">
                    {{ $siswa->nis }}
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="text-xs text-muted mb-1">
                    <i class="fas fa-user text-primary"></i> Nama Siswa
                </div>
                <div class="h6 mb-0 font-weight-bold">
                    {{ $siswa->user->name }}
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="text-xs text-muted mb-1">
                    <i class="fas fa-school text-primary"></i> Kelas
                </div>
                <div class="h6 mb-0 font-weight-bold">
                    {{ $siswa->kelasAktif->kelas->nama_kelas ?? '-' }}
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="text-xs text-muted mb-1">
                    <i class="fas fa-graduation-cap text-primary"></i> Jurusan
                </div>
                <div class="h6 mb-0 font-weight-bold">
                    {{ $siswa->jurusan->nama_jurusan ?? '-' }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
@php
    $totalMapel = $nilai->count();
    $rataRata = $nilai->avg('rata_rata') ?? 0;
    $tertinggi = $nilai->max('rata_rata') ?? 0;
    $terendah = $nilai->min('rata_rata') ?? 0;
@endphp

<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Mapel
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalMapel }}</div>
                        <div class="text-xs text-muted mt-1">Mata Pelajaran</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-book fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Rata-rata
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($rataRata, 2) }}</div>
                        <div class="text-xs text-muted mt-1">
                            @php
                                $grade = '';
                                if ($rataRata >= 90) $grade = 'A (Sangat Baik)';
                                elseif ($rataRata >= 80) $grade = 'B (Baik)';
                                elseif ($rataRata >= 70) $grade = 'C (Cukup)';
                                elseif ($rataRata >= 60) $grade = 'D (Kurang)';
                                else $grade = 'E (Sangat Kurang)';
                            @endphp
                            {{ $grade }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Tertinggi
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($tertinggi, 2) }}</div>
                        <div class="text-xs text-muted mt-1">
                            @php
                                if ($tertinggi >= 90) $gradeTertinggi = 'A';
                                elseif ($tertinggi >= 80) $gradeTertinggi = 'B';
                                elseif ($tertinggi >= 70) $gradeTertinggi = 'C';
                                elseif ($tertinggi >= 60) $gradeTertinggi = 'D';
                                else $gradeTertinggi = 'E';
                            @endphp
                            Predikat: {{ $gradeTertinggi }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Terendah
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($terendah, 2) }}</div>
                        <div class="text-xs text-muted mt-1">
                            @php
                                if ($terendah >= 90) $gradeTerendah = 'A';
                                elseif ($terendah >= 80) $gradeTerendah = 'B';
                                elseif ($terendah >= 70) $gradeTerendah = 'C';
                                elseif ($terendah >= 60) $gradeTerendah = 'D';
                                else $gradeTerendah = 'E';
                            @endphp
                            Predikat: {{ $gradeTerendah }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card shadow">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table"></i> Daftar Nilai Per Mata Pelajaran
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="bg-light">
                    <tr class="text-center">
                        <th width="5%">No</th>
                        <th class="text-left" width="20%">Mata Pelajaran</th>
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
                    @forelse($nilai as $index => $n)
                    <tr>
                        <td class="text-center align-middle">{{ $index + 1 }}</td>
                        <td class="align-middle">
                            <strong>{{ $n->mapel->nama_mapel ?? '-' }}</strong>
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge badge-light px-3 py-2">{{ $n->nilai_tugas ?? '-' }}</span>
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge badge-light px-3 py-2">{{ $n->nilai_praktikum ?? '-' }}</span>
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge badge-light px-3 py-2">{{ $n->nilai_uts ?? '-' }}</span>
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge badge-light px-3 py-2">{{ $n->nilai_uas ?? '-' }}</span>
                        </td>
                        <td class="text-center align-middle">
                            @if($n->sikap)
                                @php
                                    $badgeClass = match($n->sikap) {
                                        'A' => 'badge-success',
                                        'B' => 'badge-info',
                                        'C' => 'badge-warning',
                                        'D' => 'badge-danger',
                                        'E' => 'badge-dark',
                                        default => 'badge-secondary'
                                    };

                                    $sikapLabel = match($n->sikap) {
                                        'A' => 'A (Sangat Baik)',
                                        'B' => 'B (Baik)',
                                        'C' => 'C (Cukup)',
                                        'D' => 'D (Kurang)',
                                        'E' => 'E (Sangat Kurang)',
                                        default => $n->sikap
                                    };
                                @endphp

                                <span class="badge {{ $badgeClass }} px-3 py-2" title="{{ $sikapLabel }}">
                                    {{ $n->sikap }}
                                </span>
                            @else
                                <span class="badge badge-secondary px-3 py-2">-</span>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            @php
                                $color = 'secondary';
                                if ($n->rata_rata >= 85) $color = 'success';
                                elseif ($n->rata_rata >= 75) $color = 'info';
                                elseif ($n->rata_rata >= 60) $color = 'warning';
                                else $color = 'danger';
                            @endphp
                            <span class="badge badge-{{ $color }} px-3 py-2">
                                {{ number_format($n->rata_rata, 2) }}
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge badge-{{ $n->grade == 'A' ? 'success' : ($n->grade == 'B' ? 'info' : ($n->grade == 'C' ? 'warning' : 'danger')) }} px-3 py-2">
                                {{ $n->grade }}
                            </span>
                        </td>
                        <td class="text-center align-middle">
    <button class="btn btn-sm btn-warning mr-1 edit-nilai-btn"
            data-nilai='@json($n)'
            title="Edit">
        <i class="fas fa-edit"></i>
    </button>
    <button class="btn btn-sm btn-danger delete-nilai-btn"
            data-id="{{ $n->id }}"
            title="Hapus">
        <i class="fas fa-trash"></i>
    </button>
</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                            <h6 class="text-gray-600">Belum ada data nilai</h6>
                            <p class="text-muted small mb-0">Silakan tambah nilai menggunakan tombol di atas</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Nilai -->
<div class="modal fade" id="tambahNilaiModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle"></i> Tambah Nilai
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('nilai.store') }}" method="POST">
                @csrf
                <input type="hidden" name="nis" value="{{ $siswa->nis }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small mb-2">Mata Pelajaran <span class="text-danger">*</span></label>
                            <select name="id_mata_pelajaran" class="form-control" required>
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                @foreach(\App\Models\MataPelajaran::all() as $mapel)
                                    <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small mb-2">Guru (NIP) <span class="text-danger">*</span></label>
                            <select name="nip" class="form-control" required>
                                <option value="">-- Pilih Guru --</option>
                                @foreach(\App\Models\Guru::all() as $guru)
                                    <option value="{{ $guru->nip }}">{{ $guru->nip }} - {{ $guru->user->name ?? $guru->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small mb-2">Tahun Ajaran <span class="text-danger">*</span></label>
                            <select name="tahun_ajaran" class="form-control" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                @php
                                    $tahunAjaranAktif = (now()->month >= 7)
                                        ? now()->year . '/' . (now()->year + 1)
                                        : (now()->year - 1) . '/' . now()->year;
                                    $listTahunAjaran = \App\Models\Kelas::select('tahun_ajaran')
                                        ->distinct()
                                        ->orderBy('tahun_ajaran', 'desc')
                                        ->pluck('tahun_ajaran');
                                @endphp
                                @foreach($listTahunAjaran as $ta)
                                    <option value="{{ $ta }}" {{ $ta == $tahunAjaranAktif ? 'selected' : '' }}>
                                        {{ $ta }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small mb-2">Semester <span class="text-danger">*</span></label>
                            <select name="semester" class="form-control" required>
                                <option value="Ganjil">Semester Ganjil</option>
                                <option value="Genap">Semester Genap</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small mb-2">Nilai Tugas</label>
                            <input type="number" name="nilai_tugas" class="form-control" min="0" max="100" placeholder="0-100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small mb-2">Nilai Praktikum</label>
                            <input type="number" name="nilai_praktikum" class="form-control" min="0" max="100" placeholder="0-100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small mb-2">Nilai UTS</label>
                            <input type="number" name="nilai_uts" class="form-control" min="0" max="100" placeholder="0-100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small mb-2">Nilai UAS</label>
                            <input type="number" name="nilai_uas" class="form-control" min="0" max="100" placeholder="0-100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small mb-2">Sikap</label>
                            <select name="sikap" class="form-control">
                                <option value="">-- Pilih Sikap --</option>
                                <option value="A">A (Sangat Baik)</option>
                                <option value="B">B (Baik)</option>
                                <option value="C">C (Cukup)</option>
                                <option value="D">D (Kurang)</option>
                                <option value="E">E (Sangat Kurang)</option>
                            </select>
                        </div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i>
                        Rata-rata dan Grade akan dihitung otomatis oleh sistem
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Nilai -->
<div class="modal fade" id="editNilaiModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">
                    <i class="fas fa-edit"></i> Edit Nilai
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editNilaiForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="font-weight-bold small mb-2">Mata Pelajaran</label>
                            <input type="text" id="edit_mapel" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small mb-2">Nilai Tugas</label>
                            <input type="number" name="nilai_tugas" id="edit_tugas" class="form-control" min="0" max="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small mb-2">Nilai Praktikum</label>
                            <input type="number" name="nilai_praktikum" id="edit_praktikum" class="form-control" min="0" max="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small mb-2">Nilai UTS</label>
                            <input type="number" name="nilai_uts" id="edit_uts" class="form-control" min="0" max="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small mb-2">Nilai UAS</label>
                            <input type="number" name="nilai_uas" id="edit_uas" class="form-control" min="0" max="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small mb-2">Sikap</label>
                            <select name="sikap" id="edit_sikap" class="form-control">
                                <option value="">-- Pilih Sikap --</option>
                                <option value="A">A (Sangat Baik)</option>
                                <option value="B">B (Baik)</option>
                                <option value="C">C (Cukup)</option>
                                <option value="D">D (Kurang)</option>
                                <option value="E">E (Sangat Kurang)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Delete Confirmation -->
<div class="modal fade" id="deleteNilaiModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="deleteNilaiForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p class="mb-3">Apakah Anda yakin ingin menghapus data nilai ini?</p>
                    <p class="text-danger small mb-0">
                        <i class="fas fa-exclamation-circle"></i>
                        Data yang dihapus tidak dapat dikembalikan!
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
$(document).ready(function() {
    // Edit Nilai menggunakan jQuery event handler
    $(document).on('click', '.edit-nilai-btn', function() {
        // Parse data dari atribut data-nilai
        const data = $(this).data('nilai');
        const id = data.id;

        // Set nilai form
        $('#edit_mapel').val(data.mapel?.nama_mapel || '-');
        $('#edit_tugas').val(data.nilai_tugas || '');
        $('#edit_praktikum').val(data.nilai_praktikum || '');
        $('#edit_uts').val(data.nilai_uts || '');
        $('#edit_uas').val(data.nilai_uas || '');
        $('#edit_sikap').val(data.sikap || '');

        // Set action form
        $('#editNilaiForm').attr('action', '{{ route("nilai.update", ["id" => "__ID__"]) }}'.replace('__ID__', id));

        // Tampilkan modal
        $('#editNilaiModal').modal('show');
    });

    // Delete Nilai
    $(document).on('click', '.delete-nilai-btn', function() {
        const id = $(this).data('id');
        $('#deleteNilaiForm').attr('action', '{{ route("nilai.delete", ["id" => "__ID__"]) }}'.replace('__ID__', id));
        $('#deleteNilaiModal').modal('show');
    });

    // Tooltip
    $('[title]').tooltip({
        placement: 'top',
        trigger: 'hover'
    });
});
</script>
@endpush
