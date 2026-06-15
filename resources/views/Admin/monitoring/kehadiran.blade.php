@extends('layouts.layoutsadmin.app')

@section('title')
<title>Sistem Akademik - Monitoring Kehadiran</title>
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-calendar-check"></i> Monitoring Kehadiran Siswa
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

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
@endif

<!-- Statistics Cards - Mirror dari Screenshot Nilai -->
<div class="row">
    <!-- Card 1: RATA-RATA KEHADIRAN -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            RATA-RATA KEHADIRAN
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            @php
                                $rataRata = $siswaData->count() > 0 ?
                                    round($siswaData->avg('rasio'), 1) : 0;
                            @endphp
                            {{ $rataRata }}%
                        </div>
                        <div class="text-xs text-muted mt-1">
                            Seluruh siswa dalam kelas
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: KEHADIRAN > 90% -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            KEHADIRAN > 90%
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            @php
                                $diatas90 = $siswaData->filter(function($siswa) {
                                    return $siswa['rasio'] >= 90;
                                })->count();
                            @endphp
                            {{ $diatas90 }}
                        </div>
                        <div class="text-xs text-muted mt-1">
                            @if($siswaData->count() > 0)
                                {{ round(($diatas90 / $siswaData->count()) * 100, 1) }}% dari total
                            @else
                                0% dari total
                            @endif
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-trophy fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: PERLU PERHATIAN -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            PERLU PERHATIAN
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            @php
                                $perluPerhatian = $siswaData->filter(function($siswa) {
                                    return $siswa['rasio'] < 75;
                                })->count();
                            @endphp
                            {{ $perluPerhatian }}
                        </div>
                        <div class="text-xs text-muted mt-1">
                            Kehadiran di bawah 75%
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: ALPA TERTINGGI -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            ALPA TERTINGGI
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            @php
                                $maxAlpha = $siswaData->max('alpa') ?? 0;
                            @endphp
                            {{ $maxAlpha }}
                        </div>
                        <div class="text-xs text-muted mt-1">
                            @if($maxAlpha > 0)
                                @php
                                    $siswaAlpha = $siswaData->where('alpa', $maxAlpha)->first();
                                @endphp
                                {{ $siswaAlpha['nama'] ?? 'Siswa' }}
                            @else
                                Tidak ada alpha
                            @endif
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Card - Sederhana -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-filter"></i> Pilih Periode Kehadiran
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('MonitoringKehadiran') }}" id="filterForm">
            <div class="row align-items-end">
                <!-- Tahun Ajaran -->
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

                <!-- Kelas -->
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

                <!-- Semester -->
                <div class="col-md-2 mb-3">
                    <label class="font-weight-bold small mb-2">
                        <i class="fas fa-calendar-alt"></i> Semester
                    </label>
                    <select name="semester" class="form-control" onchange="this.form.submit()">
                        <option value="Ganjil" {{ $semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap" {{ $semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>

                <!-- Bulan -->
                <div class="col-md-2 mb-3">
                    <label class="font-weight-bold small mb-2">
                        <i class="fas fa-calendar-day"></i> Bulan
                    </label>
                    <select name="bulan" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Semua Bulan --</option>
                        @foreach($bulanList as $key => $namaBulan)
                            <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>
                                {{ $namaBulan }}
                            </option>
                        @endforeach
                    </select>
                </div>


            </div>
        </form>
    </div>
</div>

@if($kelas && $tahunAjaran && $semester)
<!-- Info Card Sederhana -->
<div class="card border-left-primary shadow mb-4">
    <div class="card-body py-3">
        <div class="row">
            <!-- Info Periode -->
            <div class="col-md-3 mb-2 text-center">
                <div class="text-xs text-muted mb-1">
                    <i class="fas fa-calendar-alt"></i> Periode
                </div>
                <div class="h6 mb-0 font-weight-bold text-primary">
                    {{ $semester }} {{ $tahunAjaran }}
                    @if($bulan)
                        <br>
                        <small class="text-muted">Bulan: {{ $bulanList[$bulan] ?? $bulan }}</small>
                    @endif
                </div>
            </div>

            <!-- Info Kelas -->
            <div class="col-md-3 mb-2 text-center">
                <div class="text-xs text-muted mb-1">
                    <i class="fas fa-school"></i> Kelas
                </div>
                <div class="h6 mb-0 font-weight-bold text-primary">
                    {{ $listKelas->where('id', $kelas)->first()->nama_kelas ?? '-' }}
                </div>
            </div>

            <!-- Total Siswa -->
            <div class="col-md-3 mb-2 text-center">
                <div class="text-xs text-muted mb-1">
                    <i class="fas fa-users"></i> Total Siswa
                </div>
                <div class="h6 mb-0 font-weight-bold text-primary">
                    {{ $siswaData->count() }} Siswa
                </div>
            </div>

            <!-- Hari Efektif -->
            <div class="col-md-3 mb-2 text-center">
                <div class="text-xs text-muted mb-1">
                    <i class="fas fa-calendar-check"></i> Hari Efektif
                </div>
                <div class="h6 mb-0 font-weight-bold text-primary">
                    {{ $hariEfektif }} Hari
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistik Sederhana -->
@if($siswaData->count() > 0)
<div class="row mb-4">
    <!-- Ringkasan Kehadiran -->
    <div class="col-md-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie"></i> Ringkasan Kehadiran
                </h6>
            </div>
            <div class="card-body">
                @php
                    $totalHadir = $siswaData->sum('hadir');
                    $totalSakit = $siswaData->sum('sakit');
                    $totalIzin = $siswaData->sum('izin');
                    $totalAlpa = $siswaData->sum('alpa');
                    $totalSemua = $totalHadir + $totalSakit + $totalIzin  + $totalAlpa;
                @endphp

                <div class="chart-pie mb-4">
                    <canvas id="kehadiranChart"></canvas>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="small mb-2">
                            <span class="badge badge-success mr-2">●</span>
                            Hadir: <strong>{{ $totalHadir }}</strong>
                            @if($totalSemua > 0)
                                <span class="text-muted">({{ round(($totalHadir/$totalSemua)*100, 1) }}%)</span>
                            @endif
                        </div>
                        <div class="small mb-2">
                            <span class="badge badge-warning mr-2">●</span>
                            Sakit: <strong>{{ $totalSakit }}</strong>
                            @if($totalSemua > 0)
                                <span class="text-muted">({{ round(($totalSakit/$totalSemua)*100, 1) }}%)</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="small mb-2">
                            <span class="badge badge-info mr-2">●</span>
                            Izin: <strong>{{ $totalIzin }}</strong>
                            @if($totalSemua > 0)
                                <span class="text-muted">({{ round(($totalIzin/$totalSemua)*100, 1) }}%)</span>
                            @endif
                        </div>
                        <div class="small mb-2">
                            <span class="badge badge-danger mr-2">●</span>
                            Alpha: <strong>{{ $totalAlpa }}</strong>
                            @if($totalSemua > 0)
                                <span class="text-muted">({{ round(($totalAlpa/$totalSemua)*100, 1) }}%)</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performers -->
    <div class="col-md-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-trophy"></i> Performa Siswa
                </h6>
            </div>
            <div class="card-body">
                <!-- Siswa dengan Kehadiran Terbaik -->
                <div class="mb-4">
                    <h6 class="text-success mb-3">
                        <i class="fas fa-check-circle"></i> 5 Kehadiran Terbaik
                    </h6>
                    @php
                        $topSiswa = $siswaData->sortByDesc('rasio')->take(5);
                    @endphp
                    @foreach($topSiswa as $index => $siswa)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="small">
                                <strong>{{ $index + 1 }}.</strong> {{ $siswa['nama'] }}
                            </div>
                            <div>
                                <span class="badge badge-success">
                                    {{ number_format($siswa['rasio'], 1) }}%
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Siswa Perlu Perhatian -->
                <div>
                    <h6 class="text-danger mb-3">
                        <i class="fas fa-exclamation-triangle"></i> Perlu Perhatian
                    </h6>
                    @php
                        $perluPerhatian = $siswaData->where('rasio', '<', 75)->sortBy('rasio')->take(5);
                    @endphp
                    @if($perluPerhatian->count() > 0)
                        @foreach($perluPerhatian as $siswa)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="small">
                                    <i class="fas fa-user mr-1"></i> {{ $siswa['nama'] }}
                                </div>
                                <div>
                                    <span class="badge badge-danger">
                                        {{ number_format($siswa['rasio'], 1) }}%
                                    </span>
                                    <small class="text-muted ml-1">
                                        (Alpha: {{ $siswa['alpa'] }})
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-2">
                            <i class="fas fa-smile"></i> Semua siswa memiliki kehadiran baik
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endif

<!-- Table Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table"></i> Data Kehadiran Siswa
            @if($bulan)
                <small class="text-muted ml-2">
                    Bulan: {{ $bulanList[$bulan] ?? $bulan }}
                </small>
            @endif
        </h6>
        @if($kelas && $siswaData->count() > 0)
        <span class="badge badge-primary">
            <i class="fas fa-users"></i> {{ $siswaData->count() }} Siswa
        </span>
        @endif
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
            <table class="table table-bordered table-hover" id="tabelKehadiran">
                <thead class="bg-light">
                    <tr class="text-center">
                        <th width="5%">No</th>
                        <th width="15%">NIS</th>
                        <th class="text-left" width="25%">Nama Siswa</th>
                        <th width="9%">Hadir</th>
                        <th width="9%">Sakit</th>
                        <th width="9%">Izin</th>
                        <th width="9%">Alpha</th>
                        <th width="9%">Total</th>
                        <th width="10%">Kehadiran</th>
                        <th width="5%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswaData as $index => $siswa)
                    @php
                        $total = $siswa['hadir'] + $siswa['sakit'] + $siswa['izin'] + $siswa['alpa'];
                    @endphp
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
                            <span class="badge badge-success px-3">{{ $siswa['hadir'] }}</span>
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge badge-warning px-3">{{ $siswa['sakit'] }}</span>
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge badge-info px-3">{{ $siswa['izin'] }}</span>
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge badge-danger px-3">{{ $siswa['alpa'] }}</span>
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge badge-primary px-3">{{ $total }}</span>
                        </td>
                        <td class="text-center align-middle">
                            @php
                                $color = 'secondary';
                                if ($siswa['rasio'] >= 90) $color = 'success';
                                elseif ($siswa['rasio'] >= 80) $color = 'info';
                                elseif ($siswa['rasio'] >= 70) $color = 'warning';
                                else $color = 'danger';

                                $icon = 'fa-check';
                                if ($siswa['rasio'] >= 90) $icon = 'fa-trophy';
                                elseif ($siswa['rasio'] < 70) $icon = 'fa-exclamation-triangle';
                            @endphp
                            <span class="badge badge-{{ $color }} px-3 py-1">
                                <i class="fas {{ $icon }} mr-1"></i>
                                {{ number_format($siswa['rasio'], 1) }}%
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            <a href="{{ route('detail-kehadiran', $siswa['id']) }}"
                                class="btn btn-sm btn-info"
                                title="Detail Kehadiran"
                                data-toggle="tooltip">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-5">
                            @if($kelas)
                                <i class="fas fa-calendar-times fa-3x mb-3 text-gray-300"></i>
                                <h6 class="text-gray-600">Belum ada data kehadiran</h6>
                                <p class="small text-muted mb-0">
                                    @if($bulan)
                                        Untuk bulan {{ $bulanList[$bulan] ?? $bulan }}
                                    @else
                                        Untuk periode yang dipilih
                                    @endif
                                </p>
                            @else
                                <i class="fas fa-filter fa-3x mb-3 text-gray-300"></i>
                                <h6 class="text-gray-600">Pilih periode terlebih dahulu</h6>
                                <p class="small text-muted mb-0">
                                    Gunakan filter di atas untuk menampilkan data kehadiran
                                </p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($siswaData->count() > 0)
                <tfoot class="bg-light">
                    <tr>
                        <th colspan="3" class="text-right">Total:</th>
                        <th class="text-center">{{ $siswaData->sum('hadir') }}</th>
                        <th class="text-center">{{ $siswaData->sum('sakit') }}</th>
                        <th class="text-center">{{ $siswaData->sum('izin') }}</th>
                        <th class="text-center">{{ $siswaData->sum('alpa') }}</th>
                        <th class="text-center">
                            @php
                                $grandTotal = $siswaData->sum('hadir') + $siswaData->sum('sakit') +
                                            $siswaData->sum('izin') + $siswaData->sum('alpa');
                            @endphp
                            {{ $grandTotal }}
                        </th>
                        <th class="text-center">
                            @php
                                $rataRataKelas = $siswaData->avg('rasio');
                            @endphp
                            <span class="badge badge-primary px-3 py-1">
                                {{ number_format($rataRataKelas, 1) }}%
                            </span>
                        </th>
                        <th></th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .chart-pie {
        position: relative;
        height: 200px;
        width: 100%;
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    .badge {
        font-size: 0.85em;
    }

    .card {
        border-radius: 10px;
    }

    .card-header {
        border-top-left-radius: 10px !important;
        border-top-right-radius: 10px !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Search functionality
    $('#searchSiswa').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#tabelKehadiran tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    // Tooltip
    $('[data-toggle="tooltip"]').tooltip();

    // Chart Kehadiran
    @if($siswaData->count() > 0)
    var ctx = document.getElementById('kehadiranChart');
    if (ctx) {
        var myPieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Sakit', 'Izin', 'Alpha'],
                datasets: [{
                    data: [{{ $totalHadir }},{{ $totalSakit }}, {{ $totalIzin }},  {{ $totalAlpa }}],
                    backgroundColor: ['#1cc88a', '#f6c23e',  '#36b9cc', '#e74a3b'],
                    hoverBackgroundColor: ['#17a673', '#dda20a', '#2c9faf', '#be2617'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: false
                },
                cutoutPercentage: 70,
            },
        });
    }
    @endif

    // Sort table by percentage
    $('#tabelKehadiran th').click(function() {
        var table = $(this).parents('table').eq(0);
        var rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()));
        this.asc = !this.asc;
        if (!this.asc) {
            rows = rows.reverse();
        }
        for (var i = 0; i < rows.length; i++) {
            table.append(rows[i]);
        }
    });

    function comparer(index) {
        return function(a, b) {
            var valA = getCellValue(a, index), valB = getCellValue(b, index);
            return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB);
        };
    }

    function getCellValue(row, index) {
        return $(row).children('td').eq(index).text();
    }
});
</script>
@endpush
