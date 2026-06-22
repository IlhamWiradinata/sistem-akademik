@extends('Layouts.LayoutsSiswa.app')

@section('title')
<title>Sistem Akademik - Nilai Akademik Siswa</title>
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-chart-line"></i> Nilai Akademik
    </h1>
    <span class="d-none d-sm-inline-block badge badge-success badge-lg">
        <i class="fas fa-graduation-cap"></i> {{ request('semester') ?? 'Semua Semester' }}
    </span>
</div>

<!-- Profile Card -->
<div class="card border-left-primary shadow mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-4">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Nama Siswa</div>
                <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $siswa->user->name }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">NIS</div>
                <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $siswa->nis }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Kelas & Jurusan</div>
                <div class="h6 mb-0 font-weight-bold text-gray-800">
                    {{ $kelasTerakhir->nama_kelas ?? '-' }}
                    @if(isset($kelasTerakhir->nama_jurusan) && $kelasTerakhir->nama_jurusan)
                        / {{ $kelasTerakhir->nama_jurusan }}
                    @elseif($siswa->jurusan)
                        / {{ $siswa->jurusan->nama_jurusan ?? '-' }}
                    @else
                        / -
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    @php
        $totalMapel = $nilai->count();
        $rataRata = $nilai->avg('rata_rata') ?? 0;

        // Nilai sudah dihitung di controller
        // $nilaiTertinggi = $nilai->max('rata_rata') ?? 0;
        // $nilaiTerendah = $nilai->min('rata_rata') ?? 0;
    @endphp

    <!-- Total Mata Pelajaran -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Mata Pelajaran</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalMapel }}</div>
                        <div class="text-muted small mt-1">Mapel</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-book fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rata-rata Nilai -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Rata-rata Nilai</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($rataRata, 2) }}</div>
                        <div class="text-muted small mt-1">
                            @if($rataRata >= 85)
                                <span class="text-success">Sangat Baik</span>
                            @elseif($rataRata >= 75)
                                <span class="text-info">Baik</span>
                            @else
                                <span class="text-warning">Cukup</span>
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

    <!-- Nilai Tertinggi -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Nilai Tertinggi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($nilaiTertinggi, 2) }}</div>
                        <div class="text-muted small mt-1">
                            @if(isset($sumberNilaiTertinggi) && $sumberNilaiTertinggi)
                                <i class="fas fa-star text-warning"></i> {{ $sumberNilaiTertinggi }}
                            @else
                                Best Score
                            @endif
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nilai Terendah -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Nilai Terendah</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($nilaiTerendah, 2) }}</div>
                        <div class="text-muted small mt-1">
                            @if(isset($sumberNilaiTerendah) && $sumberNilaiTerendah)
                                <i class="fas fa-exclamation-triangle text-danger"></i> {{ $sumberNilaiTerendah }}
                            @else
                                Perlu Ditingkatkan
                            @endif
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

<!-- Filter Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-filter"></i> Filter Data Nilai
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('Nilai') }}">
            <div class="row">
            <div class="col-md-3">
                <label class="font-weight-bold small mb-2">Semester</label>
                <select name="semester" class="form-control">
                    <option value="">Semua Semester</option>
                    <option value="Ganjil" {{ request('semester') == 'Ganjil' ? 'selected' : '' }}>
                        Semester Ganjil
                    </option>
                    <option value="Genap" {{ request('semester') == 'Genap' ? 'selected' : '' }}>
                        Semester Genap
                    </option>
                </select>
            </div>
                <div class="col-md-3">
                    <label class="font-weight-bold small mb-2">Kelas</label>
                    <select name="kelas" class="form-control">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelasList as $k)
                            <option value="{{ $k->id }}"
                                {{ request('kelas') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="font-weight-bold small mb-2">Tahun Ajaran</label>
                    <select name="tahun_ajaran" class="form-control">
                        <option value="">Semua Tahun Ajaran</option>
                        @foreach ($tahunAjaranList as $tahun)
                            <option value="{{ $tahun }}" {{ request('tahun_ajaran') == $tahun ? 'selected' : '' }}>
                                {{ $tahun }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Nilai -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table"></i> Detail Nilai Per Mata Pelajaran
        </h6>
        <span class="badge badge-primary">{{ $totalMapel }} Mata Pelajaran</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-primary text-white">
                    <tr class="text-center">
                        <th class="align-middle" width="5%">No</th>
                        <th class="text-left align-middle" width="20%">Mata Pelajaran</th>
                        <th class="align-middle" width="8%">Tugas</th>
                        <th class="align-middle" width="8%">Praktikum</th>
                        <th class="align-middle" width="8%">UTS</th>
                        <th class="align-middle" width="8%">UAS</th>
                        <th class="align-middle" width="8%">Sikap</th>
                        <th class="align-middle" width="10%">Rata-rata</th>
                        <th class="align-middle" width="8%">Grade</th>
                        <th class="align-middle" width="10%">Semester</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($nilai as $index => $n)
                    <tr>
                        <td class="text-center align-middle">{{ $index + 1 }}</td>
                        <td class="align-middle">
                            <strong>{{ $n->mapel->nama_mapel }}</strong>
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge badge-light">{{ $n->nilai_tugas }}</span>
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge badge-light">{{ $n->nilai_praktikum }}</span>
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge badge-light">{{ $n->nilai_uts }}</span>
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge badge-light">{{ $n->nilai_uas }}</span>
                        </td>
                        <td class="text-center align-middle">
                            @if($n->sikap == 'A')
                                <span class="badge badge-success">{{ $n->sikap }}</span>
                            @elseif($n->sikap == 'B')
                                <span class="badge badge-info">{{ $n->sikap }}</span>
                            @elseif($n->sikap == 'C')
                                <span class="badge badge-warning">{{ $n->sikap }}</span>
                            @else
                                <span class="badge badge-secondary">{{ $n->sikap }}</span>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            <strong class="text-{{ $n->rata_rata >= 85 ? 'success' : ($n->rata_rata >= 75 ? 'info' : 'warning') }}">
                                {{ number_format($n->rata_rata, 2) }}
                            </strong>
                        </td>
                        <td class="text-center align-middle">
                            @if($n->grade == 'A')
                                <span class="badge badge-success px-3 py-2">{{ $n->grade }}</span>
                            @elseif($n->grade == 'B')
                                <span class="badge badge-info px-3 py-2">{{ $n->grade }}</span>
                            @elseif($n->grade == 'C')
                                <span class="badge badge-warning px-3 py-2">{{ $n->grade }}</span>
                            @elseif($n->grade == 'D')
                                <span class="badge badge-danger px-3 py-2">{{ $n->grade }}</span>
                            @else
                                <span class="badge badge-secondary px-3 py-2">{{ $n->grade }}</span>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge badge-primary">{{ $n->semester }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted mb-0">Belum ada data nilai untuk filter yang dipilih</p>
                            <small class="text-muted">Coba pilih semester atau mata pelajaran lain</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@if($nilai->count() > 0)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('nilaiChart');
    if (!ctx) return;

    const mapelNames = [
        @foreach($nilai as $n)
            "{{ $n->mapel->nama_mapel }}",
        @endforeach
    ];

    const nilaiData = [
        @foreach($nilai as $n)
            {{ $n->rata_rata }},
        @endforeach
    ];

    new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: mapelNames,
            datasets: [{
                label: 'Rata-rata Nilai',
                data: nilaiData,
                backgroundColor: '#4e73df',
                borderColor: '#4e73df',
                borderWidth: 1,
                maxBarThickness: 50
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        stepSize: 10
                    },
                    grid: {
                        color: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    },
                    grid: {
                        display: false,
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
                    callbacks: {
                        label: function(context) {
                            return 'Nilai: ' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endif
