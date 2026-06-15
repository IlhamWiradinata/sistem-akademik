@extends('layouts.layoutssiswa.app')
@section('title')
<title>Sistem Akademik - Dashboard Siswa</title>
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-tachometer-alt"></i> Dashboard Siswa
    </h1>
    <span class="d-none d-sm-inline-block badge badge-primary badge-lg">
        <i class="far fa-calendar-alt"></i> {{ date('d F Y') }}
    </span>
</div>

@if(!$profile)
<!-- Alert jika belum ada profil -->
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle"></i>
    Profil siswa belum lengkap. Silakan lengkapi <a href="{{ route('ProfileSiswa') }}">profil Anda</a>.
</div>
@else
<!-- Welcome Card -->
<div class="card border-left-primary shadow mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Selamat Datang</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ Auth::user()->name }}</div>
                <div class="text-muted small mt-1">
                    <i class="fas fa-id-badge"></i> NIS: {{ $profile->nis }} |
                    <i class="fas fa-school"></i> {{ $kelasAktif->nama_kelas ?? 'Belum ada kelas' }} |
                    <i class="fas fa-book-open"></i> Semester {{ $semester }}
                    @if(isset($kelasAktif->tahun_ajaran))
                    | <i class="fas fa-calendar"></i> {{ $kelasAktif->tahun_ajaran }}
                    @endif
                </div>
            </div>
            <div class="col-auto">
                <i class="fas fa-user-graduate fa-3x text-primary" style="opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</div>

<!-- DEBUG DATA KEHADIRAN (Hanya untuk testing) -->
@php
    $totalKehadiran = $kehadiran->hadir + $kehadiran->izin + $kehadiran->sakit + $kehadiran->alpha;
@endphp
<div class="alert alert-info d-none">
    <strong>Debug Info:</strong>
    Hadir: {{ $kehadiran->hadir }},
    Izin: {{ $kehadiran->izin }},
    Sakit: {{ $kehadiran->sakit }},
    Alpha: {{ $kehadiran->alpha }},
    Total: {{ $totalKehadiran }},
    Persentase: {{ number_format($persentase, 2) }}%
</div>

<!-- Info Cards Row -->
<div class="row">
    <!-- Jumlah Mata Pelajaran Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Mata Pelajaran
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahMapel }}</div>
                        <div class="text-muted small mt-1">Semester ini</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-book fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rata-rata Nilai Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Rata-rata Nilai
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($rataRata, 2) }}</div>
                        <div class="text-muted small mt-1">
                            @if($rataRata >= 85)
                                <span class="text-success"><i class="fas fa-arrow-up"></i> Sangat Baik</span>
                            @elseif($rataRata >= 75)
                                <span class="text-info"><i class="fas fa-check"></i> Baik</span>
                            @elseif($rataRata >= 60)
                                <span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Cukup</span>
                            @else
                                <span class="text-danger"><i class="fas fa-exclamation-circle"></i> Perlu Ditingkatkan</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
</div>
    </div>

    <!-- Kehadiran Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Hadir
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $kehadiran->hadir }}</div>
                        <div class="text-muted small mt-1">Hari hadir</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ketidakhadiran Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Ketidakhadiran
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $kehadiran->izin + $kehadiran->sakit + $kehadiran->alpha }}</div>
                        <div class="text-muted small mt-1">
                            <span class="text-danger">Alpha: {{ $kehadiran->alpha }}</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <!-- Jadwal Hari Ini Card -->
        <div class="col-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-day"></i> Jadwal Pelajaran Hari Ini ({{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }})
                    </h6>
                    <a href="{{ route('JadwalPelajaran') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($jadwalHariIni) && $jadwalHariIni->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Jam</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Guru</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jadwalHariIni as $j)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::createFromFormat('H:i:s', $j->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::createFromFormat('H:i:s', $j->jam_selesai)->format('H:i') }}</td>
                                        <td>{{ $j->mapel->nama_mapel ?? '-' }}</td>
                                        <td>{{ $j->guru->user->name ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted mb-0">Tidak ada jadwal pelajaran hari ini</p>
                            <small class="text-info">Silahkan cek jadwal lengkap di menu Jadwal Pelajaran</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    <!-- Grafik Kehadiran -->
    <div class="col-xl-5 col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-calendar-check"></i> Rekapitulasi Kehadiran
                </h6>
            </div>
            <div class="card-body">
                @if($totalKehadiran > 0)
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="myPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-3">
                            <i class="fas fa-circle text-primary"></i> Hadir
                        </span>
                        <span class="mr-3">
                            <i class="fas fa-circle text-info"></i> Izin
                        </span>
                        <span class="mr-3">
                            <i class="fas fa-circle text-warning"></i> Sakit
                        </span>
                        <span class="mr-3">
                            <i class="fas fa-circle text-danger"></i> Alpha
                        </span>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">Belum ada data kehadiran</p>
                        <small class="text-info">
                            <i class="fas fa-info-circle"></i> Data kehadiran akan muncul setelah guru menginput kehadiran
                        </small>
                    </div>
                @endif
                <hr>
                <div class="row text-center">
                    <div class="col-6 border-right">
                        <div class="small text-muted">Persentase Kehadiran</div>
                        <div class="h6 mb-0 font-weight-bold text-{{ $persentase >= 80 ? 'success' : ($persentase >= 60 ? 'warning' : 'danger') }}">
                            {{ number_format($persentase, 1) }}%
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="small text-muted">Total Hari</div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                            {{ $totalKehadiran }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Nilai Per Mata Pelajaran -->
    <div class="col-xl-7 col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar"></i> Nilai Per Mata Pelajaran
                </h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                        <div class="dropdown-header">Semester:</div>
                        <a class="dropdown-item" href="#">{{ $semester }}</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($jumlahMapel > 0)
                    <div class="chart-bar">
                        <canvas id="myBarChart"></canvas>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-chart-bar fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">Belum ada data nilai</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Progress Akademik -->
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-tasks"></i> Progress Akademik Detail
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="small font-weight-bold">
                            Kehadiran <span class="float-right">{{ number_format($persentase, 0) }}%</span>
                        </h6>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-{{ $persentase >= 80 ? 'success' : ($persentase >= 60 ? 'warning' : 'danger') }}"
                                 role="progressbar"
                                 style="width: {{ min($persentase, 100) }}%"></div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="small font-weight-bold">
                            Pencapaian Nilai <span class="float-right">{{ number_format($rataRata, 0) }}%</span>
                        </h6>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-{{ $rataRata >= 85 ? 'success' : ($rataRata >= 75 ? 'info' : ($rataRata >= 60 ? 'warning' : 'danger')) }}"
                                 role="progressbar"
                                 style="width: {{ min($rataRata, 100) }}%"></div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row text-center">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="small text-muted mb-1">Nilai Tertinggi</div>
                        <div class="h5 mb-0 font-weight-bold text-success">
                            {{ number_format($nilaiTertinggi, 2) }}
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="small text-muted mb-1">Nilai Terendah</div>
                        <div class="h5 mb-0 font-weight-bold text-danger">
                            {{ number_format($nilaiTerendah, 2) }}
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="small text-muted mb-1">Status Akademik</div>
                        <div class="h6 mb-0 font-weight-bold">
                            @if($rataRata >= 85)
                                <span class="badge badge-success">Sangat Baik</span>
                            @elseif($rataRata >= 75)
                                <span class="badge badge-info">Baik</span>
                            @elseif($rataRata >= 60)
                                <span class="badge badge-warning">Cukup</span>
                            @else
                                <span class="badge badge-danger">Kurang</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="small text-muted mb-1">Status Kehadiran</div>
                        <div class="h6 mb-0 font-weight-bold">
                            @if($persentase >= 90)
                                <span class="badge badge-success">Excellent</span>
                            @elseif($persentase >= 80)
                                <span class="badge badge-info">Baik</span>
                            @elseif($persentase >= 70)
                                <span class="badge badge-warning">Cukup</span>
                            @else
                                <span class="badge badge-danger">Kurang</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart JS Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Set default colors
Chart.defaults.color = '#858796';
Chart.defaults.borderColor = '#dddfeb';

@if($totalKehadiran > 0)
// Pie Chart untuk Kehadiran
const ctxPie = document.getElementById('myPieChart');
if (ctxPie) {
    const myPieChart = new Chart(ctxPie.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
            datasets: [{
                data: [
                    {{ $kehadiran->hadir }},
                    {{ $kehadiran->izin }},
                    {{ $kehadiran->sakit }},
                    {{ $kehadiran->alpha }}
                ],
                backgroundColor: ['#4e73df', '#36b9cc', '#f6c23e', '#e74a3b'],
                hoverBackgroundColor: ['#2e59d9', '#2c9faf', '#f4b619', '#d32f2f'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    padding: 15,
                    displayColors: false,
                    caretPadding: 10,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            cutout: '80%',
        }
    });
}
@endif

@if($jumlahMapel > 0)
// Bar Chart untuk Nilai Per Mata Pelajaran
const ctxBar = document.getElementById('myBarChart');
if (ctxBar) {
    // Data dari controller
    const mapelNames = [
        @foreach($nilaiList as $n)
            @php
                // Ambil nama mapel dengan berbagai cara
                $namaMapel = 'N/A';
                if (isset($n->nama_mapel)) {
                    $namaMapel = $n->nama_mapel;
                } elseif (isset($n->mapel) && $n->mapel) {
                    if (is_object($n->mapel)) {
                        $namaMapel = $n->mapel->nama_mapel ?? $n->mapel->nama ?? 'N/A';
                    } elseif (is_array($n->mapel)) {
                        $namaMapel = $n->mapel['nama_mapel'] ?? $n->mapel['nama'] ?? 'N/A';
                    }
                } else {
                    $namaMapel = $n->nama ?? 'N/A';
                }
                // Batasi panjang nama
                $namaMapel = strlen($namaMapel) > 20 ? substr($namaMapel, 0, 17) . '...' : $namaMapel;
            @endphp
            "{{ addslashes($namaMapel) }}",
        @endforeach
    ];

    const mapelScores = [
        @foreach($nilaiList as $n)
            {{ $n->rata_rata ?? 0 }},
        @endforeach
    ];

    // Function untuk menentukan warna berdasarkan nilai
    const getColor = (score) => {
        if (score >= 85) return '#1cc88a'; // success
        if (score >= 75) return '#36b9cc'; // info
        if (score >= 60) return '#f6c23e'; // warning
        return '#e74a3b'; // danger
    };

    const backgroundColors = mapelScores.map(score => getColor(score));

    const myBarChart = new Chart(ctxBar.getContext('2d'), {
        type: 'bar',
        data: {
            labels: mapelNames,
            datasets: [{
                label: 'Nilai',
                data: mapelScores,
                backgroundColor: backgroundColors,
                borderColor: backgroundColors,
                maxBarThickness: 50,
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45,
                        font: {
                            size: 11
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    min: 0,
                    max: 100,
                    ticks: {
                        stepSize: 20,
                        callback: function(value) {
                            return value;
                        }
                    },
                    grid: {
                        color: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    padding: 15,
                    displayColors: false,
                    caretPadding: 10,
                    callbacks: {
                        label: function(context) {
                            return 'Nilai: ' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            }
        }
    });
}
@endif
</script>
@endif
@endsection
