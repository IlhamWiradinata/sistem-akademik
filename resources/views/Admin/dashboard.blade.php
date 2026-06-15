@extends('layouts.layoutsadmin.app')

@section('title')
<title>Sistem Akademik - Dashboard Admin</title>
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-tachometer-alt"></i> Dashboard Administrator
    </h1>
    <span class="d-none d-sm-inline-block badge badge-primary badge-lg">
        <i class="far fa-calendar-alt"></i> {{ date('d F Y') }}
    </span>
</div>

<!-- Welcome Card -->
<div class="card border-left-primary shadow mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Selamat Datang</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ Auth::user()->name }}</div>
                <div class="text-muted small mt-1">
                    <i class="fas fa-user-shield"></i> Role: {{ Auth::user()->role }} |
                    <i class="fas fa-building"></i> SMK Negeri 1 Cipeundeuy
                </div>
            </div>
            <div class="col-auto">
                <i class="fas fa-user-tie fa-3x text-primary" style="opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards Row -->
<div class="row">
    <!-- Total Siswa Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Siswa
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSiswa ?? 0 }}</div>
                        <div class="text-muted small mt-1">Siswa Aktif</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Guru Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Guru
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalGuru ?? 0 }}</div>
                        <div class="text-muted small mt-1">Guru Aktif</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Kelas Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Kelas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalKelas ?? 0 }}</div>
                        <div class="text-muted small mt-1">Kelas Aktif</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-school fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Mata Pelajaran Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Mata Pelajaran
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalMapel ?? 0 }}</div>
                        <div class="text-muted small mt-1">Total Mapel</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-book fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <!-- Bar Chart - Jumlah Siswa per Jurusan -->
    <div class="col-xl-7 col-lg-7 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar"></i> Jumlah Siswa Per Jurusan
                </h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                        <div class="dropdown-header">Tahun Ajaran:</div>
                        <a class="dropdown-item" href="#">{{ date('Y') }}/{{ date('Y')+1 }}</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-bar">
                    <canvas id="myBarChart"></canvas>
                </div>
                <hr>
                <div class="text-center small">
                    <span class="mr-3">
                        <i class="fas fa-circle text-primary"></i> Total: {{ $totalSiswa ?? 0 }} Siswa
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart - Persentase Kehadiran per Kelas -->
    <div class="col-xl-5 col-lg-5 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-calendar-check"></i> Persentase Kehadiran per Kelas
                </h6>
            </div>
            <div class="card-body">
                @if($kehadiranPerKelas->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-chart-bar fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted mb-2">Belum ada data kehadiran</p>
                        <small>Data akan muncul setelah ada input kehadiran</small>
                    </div>
                @else
                    <div class="chart-bar pt-4 pb-2" style="height: 300px;">
                        <canvas id="myAttendanceChart"></canvas>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6 border-right">
                            <div class="small text-muted">Rata-rata Kehadiran</div>
                            <div class="h6 mb-0 font-weight-bold text-{{ $persentase >= 80 ? 'success' : ($persentase >= 60 ? 'warning' : 'danger') }}">
                                {{ number_format($persentase, 1) }}%
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="small text-muted">Jumlah Kelas</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $kehadiranPerKelas->count() }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Additional Info Cards Row -->
<div class="row">
    <!-- Siswa per Tingkat -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-layer-group"></i> Distribusi Siswa Per Tingkat
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4 border-right">
                        <div class="small text-muted mb-1">Kelas X</div>
                        <div class="h5 mb-0 font-weight-bold text-primary">
                            {{ $siswaPerTingkat['X'] ?? 0 }}
                        </div>
                    </div>
                    <div class="col-4 border-right">
                        <div class="small text-muted mb-1">Kelas XI</div>
                        <div class="h5 mb-0 font-weight-bold text-info">
                            {{ $siswaPerTingkat['XI'] ?? 0 }}
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="small text-muted mb-1">Kelas XII</div>
                        <div class="h5 mb-0 font-weight-bold text-success">
                            {{ $siswaPerTingkat['XII'] ?? 0 }}
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    @foreach(['X' => 'primary', 'XI' => 'info', 'XII' => 'success'] as $tingkat => $color)
                    <div class="col-12 mb-2">
                        <h6 class="small font-weight-bold">
                            Kelas {{ $tingkat }}
                            <span class="float-right">
                                {{ $siswaPerTingkat[$tingkat] ?? 0 }} / {{ $totalSiswa ?? 1 }} Siswa
                            </span>
                        </h6>
                        @php
                            $persentaseTingkat = $totalSiswa > 0 ? (($siswaPerTingkat[$tingkat] ?? 0) / $totalSiswa) * 100 : 0;
                        @endphp
                        <div class="progress mb-2">
                            <div class="progress-bar bg-{{ $color }}"
                                 role="progressbar"
                                 style="width: {{ $persentaseTingkat }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

<!-- Quick Stats -->
<div class="col-lg-6 mb-4">
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-info-circle"></i> Informasi Akademik
            </h6>
        </div>
        <div class="card-body">
            <div class="list-group list-group-flush">
                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <div>
                        <i class="fas fa-graduation-cap text-primary"></i>
                        <strong>Total Jurusan</strong>
                    </div>
                    <span class="badge badge-primary badge-pill">{{ $totalJurusan }}</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <div>
                        <i class="fas fa-user-graduate text-success"></i>
                        <strong>Rata-rata Siswa per Kelas</strong>
                    </div>
                    <span class="badge badge-success badge-pill">
                        @php
                            $avgSiswa = $totalKelas > 0 ? round($totalSiswa / $totalKelas) : 0;
                        @endphp
                        {{ $avgSiswa }} Siswa
                    </span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <div>
                        <i class="fas fa-chart-line text-info"></i>
                        <strong>Tingkat Kehadiran</strong>
                    </div>
                    <span class="badge badge-{{ $persentase >= 80 ? 'success' : ($persentase >= 60 ? 'warning' : 'danger') }} badge-pill">
                        {{ number_format($persentase, 1) }}%
                    </span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <div>
                        <i class="fas fa-calendar-alt text-warning"></i>
                        <strong>Tahun Ajaran Aktif</strong>
                    </div>
                    <span class="badge badge-warning badge-pill">{{ $tahunAjaranAktif }}</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <div>
                        <i class="fas fa-clock text-secondary"></i>
                        <strong>Semester Aktif</strong>
                    </div>
                    <span class="badge badge-secondary badge-pill">{{ $semesterAktif }}</span>
                </div>
            </div>

            <!-- Informasi Tambahan -->
            <div class="mt-3 small text-muted">
                <i class="fas fa-info-circle text-primary"></i>
                <strong>Periode Akademik:</strong>
                <ul class="mb-0 pl-3">
                    <li>Semester Ganjil: Juli - Desember</li>
                    <li>Semester Genap: Januari - Juni</li>
                    <li>Tahun Ajaran: {{ $tahunAjaranAktif }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
</div>

@endsection

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Set default colors
Chart.defaults.color = '#858796';
Chart.defaults.borderColor = '#dddfeb';

// Bar Chart - Jumlah Siswa per Jurusan
const ctxBar = document.getElementById('myBarChart');
if (ctxBar) {
    const jurusanNames = [
        @foreach($siswaPerJurusan ?? [] as $item)
            "{{ $item->nama_jurusan ?? 'Unknown' }}",
        @endforeach
    ];

    const jurusanCounts = [
        @foreach($siswaPerJurusan ?? [] as $item)
            {{ $item->total ?? 0 }},
        @endforeach
    ];

    // Warna berbeda untuk setiap bar jika ada banyak jurusan
    const barColors = generateColors(jurusanNames.length);

    new Chart(ctxBar.getContext('2d'), {
        type: 'bar',
        data: {
            labels: jurusanNames,
            datasets: [{
                label: 'Jumlah Siswa',
                data: jurusanCounts,
                backgroundColor: barColors,
                hoverBackgroundColor: barColors.map(color => darkenColor(color, 20)),
                borderColor: barColors,
                borderWidth: 1,
                maxBarThickness: 50,
            }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            scales: {
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxRotation: jurusanNames.length > 3 ? 45 : 0,
                        minRotation: jurusanNames.length > 3 ? 45 : 0,
                        font: {
                            size: jurusanNames.length > 5 ? 10 : 12
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        callback: function(value) {
                            return Number.isInteger(value) ? value : '';
                        }
                    },
                    grid: {
                        color: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                    },
                    title: {
                        display: true,
                        text: 'Jumlah Siswa'
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
                    titleColor: "#6e707e",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            const label = context.dataset.label || '';
                            const value = context.parsed.y;
                            const total = jurusanCounts.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

// Pie Chart - Rekapitulasi Kehadiran
const ctxAttendance = document.getElementById('myAttendanceChart');
    if (ctxAttendance) {
    const labels = {!! json_encode($kehadiranPerKelas->pluck('nama_kelas')) !!};
    const data = {!! json_encode($kehadiranPerKelas->pluck('persentase')) !!};

    // Warna dinamis berdasarkan persentase
    const backgroundColors = data.map(val => {
        if (val >= 80) return '#1cc88a';      // hijau (success)
        if (val >= 60) return '#f6c23e';      // kuning (warning)
        return '#e74a3b';                     // merah (danger)
    });

    new Chart(ctxAttendance.getContext('2d'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Persentase Kehadiran',
                data: data,
                backgroundColor: backgroundColors,
                borderRadius: 4,
                maxBarThickness: 35,
            }]
        },
        options: {
            indexAxis: 'y', // horizontal bar
            maintainAspectRatio: false,
            responsive: true,
            scales: {
                x: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: value => value + '%'
                    },
                    grid: {
                        color: "rgb(234, 236, 244)",
                        borderDash: [2],
                    }
                },
                y: {
                    grid: {
                        display: false
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
                    titleColor: "#6e707e",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            return `Kehadiran: ${context.raw}%`;
                        }
                    }
                }
            }
        }
    });
}

// Helper functions untuk warna
function generateColors(count) {
    const colors = [
        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
        '#6f42c1', '#20c9a6', '#858796', '#5a5c69', '#3a3b45'
    ];

    if (count <= colors.length) {
        return colors.slice(0, count);
    }

    // Generate random colors jika lebih dari yang tersedia
    const generatedColors = [];
    for (let i = 0; i < count; i++) {
        const hue = Math.floor(Math.random() * 360);
        generatedColors.push(`hsl(${hue}, 70%, 60%)`);
    }
    return generatedColors;
}

function darkenColor(color, percent) {
    let r, g, b;

    if (color.startsWith('#')) {
        // Hex color
        r = parseInt(color.slice(1, 3), 16);
        g = parseInt(color.slice(3, 5), 16);
        b = parseInt(color.slice(5, 7), 16);
    } else if (color.startsWith('hsl')) {
        // HSL color
        const match = color.match(/hsl\((\d+),\s*(\d+)%,\s*(\d+)%\)/);
        if (match) {
            const h = parseInt(match[1]);
            const s = parseInt(match[2]);
            let l = parseInt(match[3]);
            l = Math.max(0, l - percent);
            return `hsl(${h}, ${s}%, ${l}%)`;
        }
    }

    // Darken RGB
    r = Math.max(0, Math.floor(r * (100 - percent) / 100));
    g = Math.max(0, Math.floor(g * (100 - percent) / 100));
    b = Math.max(0, Math.floor(b * (100 - percent) / 100));

    return `rgb(${r}, ${g}, ${b})`;
}

// Auto resize charts on window resize
window.addEventListener('resize', function() {
    const charts = Chart.instances;
    charts.forEach(chart => {
        chart.resize();
    });
});
</script>
@endpush
