@extends('layouts.layoutsadmin.app')

@section('title')
<title>Sistem Akademik - Monitoring Nilai</title>
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-chart-line"></i> Monitoring Nilai Siswa
    </h1>
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

<!-- Statistics Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Di Atas KKM
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            @php
                                $kkm = 75;
                                $diatasKKM = $siswaData->filter(function($siswa) use ($kkm) {
                                    return $siswa['rata_rata'] >= $kkm;
                                })->count();
                            @endphp
                            {{ $diatasKKM }}
                        </div>
                        <div class="text-xs text-muted mt-1">
                            {{ $siswaData->count() > 0 ? round(($diatasKKM / $siswaData->count()) * 100, 1) : 0 }}% dari total
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            Perlu Perhatian
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            @php
                                $kkm = 75;
                                $perluPerhatian = $siswaData->filter(function($siswa) use ($kkm) {
                                    return $siswa['rata_rata'] > 0 && $siswa['rata_rata'] < $kkm;
                                })->count();
                            @endphp
                            {{ $perluPerhatian }}
                        </div>
                        <div class="text-xs text-muted mt-1">
                            Nilai di bawah KKM (75)
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                            Persentase Lulus
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            @php
                                $lulus = $siswaData->filter(function($siswa) {
                                    return $siswa['rata_rata'] >= 75 && $siswa['rata_rata'] <= 100;
                                })->count();
                                $persentaseLulus = $siswaData->count() > 0 ? round(($lulus / $siswaData->count()) * 100, 1) : 0;
                            @endphp
                            {{ $persentaseLulus }}%
                        </div>
                        <div class="text-xs text-muted mt-1">
                            {{ $lulus }} dari {{ $siswaData->count() }} siswa
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Predikat Tertinggi
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            @php
                                $predikatTertinggi = '-';
                                if ($siswaData->where('rata_rata', '>', 0)->count() > 0) {
                                    $nilaiTertinggi = $siswaData->where('rata_rata', '>', 0)->max('rata_rata');
                                    if ($nilaiTertinggi >= 90) {
                                        $predikatTertinggi = 'A';
                                    } elseif ($nilaiTertinggi >= 80) {
                                        $predikatTertinggi = 'B';
                                    } elseif ($nilaiTertinggi >= 70) {
                                        $predikatTertinggi = 'C';
                                    } elseif ($nilaiTertinggi >= 60) {
                                        $predikatTertinggi = 'D';
                                    } else {
                                        $predikatTertinggi = 'E';
                                    }
                                }
                            @endphp
                            {{ $predikatTertinggi }}
                        </div>
                        <div class="text-xs text-muted mt-1">
                            Nilai: {{ $siswaData->where('rata_rata', '>', 0)->max('rata_rata') ?? '-' }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-star fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-filter"></i> Filter Data Nilai
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('MonitoringNilai') }}" id="filterForm">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="font-weight-bold small mb-2">
                        <i class="fas fa-calendar"></i> Tahun Ajaran
                    </label>
                    <select name="tahun_ajaran" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        @foreach($listTahunAjaran as $ta)
                            <option value="{{ $ta }}" {{ $tahunAjaran == $ta ? 'selected' : '' }}>
                                {{ $ta }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="font-weight-bold small mb-2">
                        <i class="fas fa-school"></i> Kelas
                    </label>
                    <select name="kelas_id" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($listKelas as $k)
                            <option value="{{ $k->id }}" {{ $kelas == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="font-weight-bold small mb-2">
                        <i class="fas fa-calendar-alt"></i> Semester
                    </label>
                    <select name="semester" id="semester" class="form-control" onchange="this.form.submit()">
                        <option value="Ganjil" {{ $semester == 'Ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
                        <option value="Genap" {{ $semester == 'Genap' ? 'selected' : '' }}>Semester Genap</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>

@if($kelas)
<!-- Info Card -->
<div class="card border-left-primary shadow mb-4">
    <div class="card-body py-3">
        <div class="row">
            <div class="col-md-2 mb-2">
                <div class="text-xs text-muted mb-1">
                    <i class="fas fa-calendar-alt text-info"></i> Periode
                </div>
                <div class="h6 mb-0 font-weight-bold">
                    {{ $semester }} {{ $tahunAjaran }}
                </div>
            </div>

            <div class="col-md-2 mb-2">
                <div class="text-xs text-muted mb-1">
                    <i class="fas fa-school text-info"></i> Kelas
                </div>
                <div class="h6 mb-0 font-weight-bold">
                    {{ $listKelas->where('id', $kelas)->first()->nama_kelas ?? '-' }}
                </div>
            </div>

            <div class="col-md-2 mb-2">
                <div class="text-xs text-muted mb-1">
                    <i class="fas fa-users text-info"></i> Total Siswa
                </div>
                <div class="h6 mb-0 font-weight-bold">
                    {{ $siswaData->count() }}
                </div>
            </div>

            <div class="col-md-2 mb-2">
                <div class="text-xs text-muted mb-1">
                    <i class="fas fa-chart-line text-info"></i> Rata-rata
                </div>
                <div class="h6 mb-0 font-weight-bold">
                    @php
                        $rataRataKelas = $siswaData->where('rata_rata', '>', 0)->avg('rata_rata');
                    @endphp
                    {{ $rataRataKelas ? number_format($rataRataKelas, 2) : '0.00' }}
                </div>
            </div>

            <div class="col-md-2 mb-2">
                <div class="text-xs text-muted mb-1">
                    <i class="fas fa-check-circle text-success"></i> Di Atas KKM
                </div>
                <div class="h6 mb-0 font-weight-bold">
                    @php
                        $kkm = 75;
                        $diatasKKM = $siswaData->filter(function($siswa) use ($kkm) {
                            return $siswa['rata_rata'] >= $kkm;
                        })->count();
                    @endphp
                    {{ $diatasKKM }}
                </div>
            </div>

            <div class="col-md-2 mb-2">
                <div class="text-xs text-muted mb-1">
                    <i class="fas fa-percentage text-primary"></i> Kelulusan
                </div>
                <div class="h6 mb-0 font-weight-bold">
                    @php
                        $lulus = $siswaData->filter(function($siswa) {
                            return $siswa['rata_rata'] >= 75;
                        })->count();
                        $persentase = $siswaData->count() > 0 ? round(($lulus / $siswaData->count()) * 100, 0) : 0;
                    @endphp
                    {{ $persentase }}%
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Distribusi Predikat -->
<div class="row mb-4">
    <div class="col-xl-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie"></i> Distribusi Predikat Nilai
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @php
                        $distribusiPredikat = [
                            'A' => ['min' => 90, 'max' => 100, 'color' => 'success', 'label' => 'Sangat Baik'],
                            'B' => ['min' => 80, 'max' => 89.99, 'color' => 'info', 'label' => 'Baik'],
                            'C' => ['min' => 70, 'max' => 79.99, 'color' => 'warning', 'label' => 'Cukup'],
                            'D' => ['min' => 60, 'max' => 69.99, 'color' => 'danger', 'label' => 'Kurang'],
                            'E' => ['min' => 0, 'max' => 59.99, 'color' => 'secondary', 'label' => 'Sangat Kurang'],
                        ];

                        $totalSiswaNilai = $siswaData->where('rata_rata', '>', 0)->count();
                    @endphp

                    @foreach($distribusiPredikat as $grade => $config)
                        @php
                            $count = $siswaData->filter(function($siswa) use ($config) {
                                return $siswa['rata_rata'] >= $config['min'] && $siswa['rata_rata'] <= $config['max'];
                            })->count();
                            $percentage = $totalSiswaNilai > 0 ? round(($count / $totalSiswaNilai) * 100, 1) : 0;
                        @endphp

                        <div class="col-md mb-3">
                            <div class="card border-left-{{ $config['color'] }} h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="text-xs font-weight-bold text-{{ $config['color'] }} text-uppercase mb-1">
                                                Predikat {{ $grade }}
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold">{{ $count }}</div>
                                            <div class="text-xs text-muted">
                                                {{ $percentage }}% • {{ $config['label'] }}
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <i class="fas fa-chart-pie fa-2x text-{{ $config['color'] }}"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Visualisasi Distribusi -->
                @if($totalSiswaNilai > 0)
                <div class="mt-3">
                    <h6 class="small font-weight-bold mb-2">Visualisasi Distribusi:</h6>
                    <div class="progress mb-2" style="height: 20px;">
                        @foreach($distribusiPredikat as $grade => $config)
                            @php
                                $count = $siswaData->filter(function($siswa) use ($config) {
                                    return $siswa['rata_rata'] >= $config['min'] && $siswa['rata_rata'] <= $config['max'];
                                })->count();
                                $percentage = $totalSiswaNilai > 0 ? ($count / $totalSiswaNilai) * 100 : 0;
                            @endphp
                            <div class="progress-bar bg-{{ $config['color'] }}"
                                 role="progressbar"
                                 style="width: {{ $percentage }}%"
                                 title="Predikat {{ $grade }}: {{ $count }} siswa ({{ number_format($percentage, 1) }}%)">
                            </div>
                        @endforeach
                    </div>
                    <div class="text-center small">
                        @foreach($distribusiPredikat as $grade => $config)
                            <span class="mr-3">
                                <i class="fas fa-square text-{{ $config['color'] }}"></i> {{ $grade }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Data Siswa -->
<div class="card shadow">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table"></i> Data Nilai Siswa
        </h6>
        <span class="badge badge-primary">{{ $siswaData->count() }} Siswa</span>
    </div>
    <div class="card-body">
        <!-- Search Box -->
        <div class="form-group mb-4">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
                <input type="text" class="form-control" id="searchSiswa"
                    placeholder="Cari berdasarkan NIS atau Nama Siswa...">
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="tabelNilai">
                <thead class="bg-light">
                    <tr class="text-center">
                        <th width="5%">No</th>
                        <th width="15%">NIS</th>
                        <th class="text-left" width="25%">Nama Siswa</th>
                        <th width="12%">Total Mapel</th>
                        <th width="12%">Rata-rata</th>
                        <th width="12%">Nilai Tertinggi</th>
                        <th width="12%">Nilai Terendah</th>
                        <th width="7%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($siswaData as $index => $siswa)
                    <tr>
                        <td class="text-center align-middle">{{ $index + 1 }}</td>
                        <td class="align-middle">
                            <span class="badge badge-secondary">{{ $siswa['nis'] }}</span>
                        </td>
                        <td class="align-middle">
                            <i class="fas fa-user-circle text-primary mr-2"></i>
                            <strong>{{ $siswa['nama'] }}</strong>
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge badge-{{ $siswa['total_mapel'] > 0 ? 'primary' : 'secondary' }} px-3 py-2">
                                {{ $siswa['total_mapel'] }}
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            @if($siswa['rata_rata'] > 0)
                                @php
                                    $color = 'secondary';
                                    if ($siswa['rata_rata'] >= 85) $color = 'success';
                                    elseif ($siswa['rata_rata'] >= 75) $color = 'info';
                                    elseif ($siswa['rata_rata'] >= 60) $color = 'warning';
                                    else $color = 'danger';
                                @endphp
                                <span class="badge badge-{{ $color }} px-3 py-2">
                                    {{ number_format($siswa['rata_rata'], 2) }}
                                </span>
                            @else
                                <span class="badge badge-secondary px-3 py-2">-</span>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            @if($siswa['nilai_tertinggi'] != '-')
                                <span class="badge badge-success px-3 py-2">{{ $siswa['nilai_tertinggi'] }}</span>
                            @else
                                <span class="badge badge-secondary px-3 py-2">-</span>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            @if($siswa['nilai_terendah'] != '-')
                                <span class="badge badge-warning px-3 py-2">{{ $siswa['nilai_terendah'] }}</span>
                            @else
                                <span class="badge badge-secondary px-3 py-2">-</span>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            <a href="{{ route('detail-nilai', $siswa['id']) }}"
                               class="btn btn-sm btn-info" title="Detail Nilai">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            @if($kelas)
                                <i class="fas fa-users-slash fa-3x mb-3 text-gray-300"></i>
                                <h6 class="text-gray-600">Tidak ada data siswa di kelas ini</h6>
                                <p class="small text-muted mb-0">Silakan input nilai untuk siswa di kelas ini</p>
                            @else
                                <i class="fas fa-filter fa-3x mb-3 text-gray-300"></i>
                                <h6 class="text-gray-600">Silakan pilih Tahun Ajaran dan Kelas terlebih dahulu</h6>
                                <p class="small text-muted mb-0">Gunakan filter di atas untuk menampilkan data</p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@else

<!-- Empty State -->
<div class="card shadow">
    <div class="card-body text-center py-5">
        <i class="fas fa-filter fa-4x text-gray-300 mb-3"></i>
        <h5 class="text-gray-600">Silakan pilih Tahun Ajaran dan Kelas</h5>
        <p class="text-muted mb-4">Gunakan filter di atas untuk menampilkan data nilai siswa</p>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Search functionality
    $('#searchSiswa').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#tabelNilai tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    // Tooltip untuk progress bars
    $('[title]').tooltip({
        placement: 'top',
        trigger: 'hover'
    });
});
</script>
@endpush
