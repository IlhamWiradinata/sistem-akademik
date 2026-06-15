@extends('layouts.layoutssiswa.app')

@section('title')
<title> Sistem Akademik - Data Kehadiran Siswa </title>
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-calendar-check"></i> Data Kehadiran
    </h1>
    <span class="d-none d-sm-inline-block badge badge-primary badge-lg">
        <i class="far fa-calendar-alt"></i> {{ $bulanList[$bulan] }} {{ $tahun }}
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
        $totalHadir = $kehadiran->where('status', 'hadir')->count();
        $totalIzin = $kehadiran->where('status', 'izin')->count();
        $totalSakit = $kehadiran->where('status', 'sakit')->count();
        $totalAlpa = $kehadiran->where('status', 'alpa')->count();
        $totalKehadiran = $kehadiran->count();
        $persentaseHadir = $totalKehadiran > 0 ? ($totalHadir / $totalKehadiran) * 100 : 0;
    @endphp

    <!-- Hadir Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Hadir</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalHadir }}</div>
                        <div class="text-muted small mt-1">Hari</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Izin Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Izin</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalIzin }}</div>
                        <div class="text-muted small mt-1">Hari</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sakit Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Sakit</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSakit }}</div>
                        <div class="text-muted small mt-1">Hari</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-notes-medical fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpha Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Alpha</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAlpa }}</div>
                        <div class="text-muted small mt-1">Hari</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
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
            <i class="fas fa-filter"></i> Filter Data Kehadiran
        </h6>
    </div>

    <div class="card-body">
        <form action="" method="GET" class="row">
            <div class="col-md-5">
                <label class="font-weight-bold small mb-2">Bulan</label>
                <select name="bulan" class="form-control">
                    @foreach ($bulanList as $key => $nama)
                        <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>
                            {{ $nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-5">
                <label class="font-weight-bold small mb-2">Tahun</label>
                <select name="tahun" class="form-control">
                    @foreach ($tahunList as $t)
                        <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>
                            {{ $t }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
        </form>
    </div>

</div>

<!-- Content Row -->
<div class="row">
    <!-- Tabel Kehadiran -->
    <div class="col-lg-7 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list"></i> Detail Kehadiran
                </h6>
                <span class="badge badge-primary">{{ $totalKehadiran }} Data</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th class="text-center" width="20%">Tanggal</th>
                                <th class="text-center" width="20%">Status</th>
                                <th width="60%">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kehadiran as $h)
                            <tr>
                                <td class="text-center align-middle">
                                    <strong>{{ \Carbon\Carbon::parse($h->tanggal)->format('d') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($h->tanggal)->format('M Y') }}</small>
                                </td>
                                <td class="text-center align-middle">
                                    @if ($h->status == 'hadir')
                                        <span class="badge badge-success px-3 py-2">
                                            <i class="fas fa-check"></i> Hadir
                                        </span>
                                    @elseif ($h->status == 'izin')
                                        <span class="badge badge-info px-3 py-2">
                                            <i class="fas fa-file-alt"></i> Izin
                                        </span>
                                    @elseif ($h->status == 'sakit')
                                        <span class="badge badge-warning px-3 py-2">
                                            <i class="fas fa-notes-medical"></i> Sakit
                                        </span>
                                    @else
                                        <span class="badge badge-danger px-3 py-2">
                                            <i class="fas fa-times"></i> Alpha
                                        </span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <small>{{ $h->keterangan ?? '-' }}</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                                    <p class="text-muted mb-0">Tidak ada data kehadiran untuk bulan ini</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Kalender & Statistik -->
    <div class="col-lg-5 mb-4">
        <!-- Persentase Kehadiran -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie"></i> Persentase Kehadiran
                </h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <h2 class="font-weight-bold text-{{ $persentaseHadir >= 80 ? 'success' : ($persentaseHadir >= 60 ? 'warning' : 'danger') }}">
                        {{ number_format($persentaseHadir, 1) }}%
                    </h2>
                    <p class="text-muted mb-0">
                        Dari {{ $totalKehadiran }} hari efektif
                    </p>
                </div>
                <div class="progress" style="height: 25px;">
                    <div class="progress-bar bg-{{ $persentaseHadir >= 80 ? 'success' : ($persentaseHadir >= 60 ? 'warning' : 'danger') }}"
                         role="progressbar"
                         style="width: {{ $persentaseHadir }}%">
                        {{ number_format($persentaseHadir, 0) }}%
                    </div>
                </div>
                <div class="mt-3">
                    @if($persentaseHadir >= 90)
                        <span class="badge badge-success px-3 py-2">
                            <i class="fas fa-star"></i> Sangat Baik
                        </span>
                    @elseif($persentaseHadir >= 80)
                        <span class="badge badge-info px-3 py-2">
                            <i class="fas fa-check"></i> Baik
                        </span>
                    @elseif($persentaseHadir >= 60)
                        <span class="badge badge-warning px-3 py-2">
                            <i class="fas fa-exclamation-triangle"></i> Cukup
                        </span>
                    @else
                        <span class="badge badge-danger px-3 py-2">
                            <i class="fas fa-times"></i> Perlu Ditingkatkan
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Kalender Kehadiran -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="far fa-calendar-alt"></i> Kalender Kehadiran
                </h6>
            </div>
            <div class="card-body">
                @php
                    use Carbon\Carbon;
                    $firstDay = Carbon::create($tahun, $bulan, 1);
                    $lastDay = $firstDay->copy()->endOfMonth();
                    $current = $firstDay->copy()->startOfWeek();
                @endphp

                <table class="table table-bordered text-center mb-3">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="py-2">Min</th>
                            <th class="py-2">Sen</th>
                            <th class="py-2">Sel</th>
                            <th class="py-2">Rab</th>
                            <th class="py-2">Kam</th>
                            <th class="py-2">Jum</th>
                            <th class="py-2">Sab</th>
                        </tr>
                    </thead>
                    <tbody>
                        @while ($current <= $lastDay)
                            <tr>
                                @for ($i = 0; $i < 7; $i++)
                                    @php
                                        $tanggal = $current->format('Y-m-d');
                                        $data = $kehadiranByDate[$tanggal] ?? null;
                                        $isCurrentMonth = $current->month == $bulan;

                                        if (!$isCurrentMonth) {
                                            $bg = '#f8f9fa';
                                            $color = '#dee2e6';
                                        } elseif ($data) {
                                            switch ($data->status) {
                                                case 'hadir': $bg = '#28a745'; $color = '#ffffff'; break;
                                                case 'izin':  $bg = '#17a2b8'; $color = '#ffffff'; break;
                                                case 'sakit': $bg = '#ffc107'; $color = '#ffffff'; break;
                                                case 'alpa':  $bg = '#dc3545'; $color = '#ffffff'; break;
                                                default: $bg = '#f8f9fa'; $color = '#6c757d';
                                            }
                                        } else {
                                            $bg = '#ffffff';
                                            $color = '#495057';
                                        }
                                    @endphp

                                    <td style="background: {{ $bg }}; color: {{ $color }}; padding: 10px; font-weight: {{ $data ? 'bold' : 'normal' }}; border: 1px solid #dee2e6;">
                                        {{ $current->day }}
                                    </td>

                                    @php $current->addDay(); @endphp
                                @endfor
                            </tr>
                        @endwhile
                    </tbody>
                </table>

                <!-- Legend -->
                <div class="text-center">
                    <div class="d-flex justify-content-center flex-wrap">
                        <span class="badge badge-success mr-2 mb-2">
                            <i class="fas fa-square"></i> Hadir
                        </span>
                        <span class="badge badge-info mr-2 mb-2">
                            <i class="fas fa-square"></i> Izin
                        </span>
                        <span class="badge badge-warning mr-2 mb-2">
                            <i class="fas fa-square"></i> Sakit
                        </span>
                        <span class="badge badge-danger mb-2">
                            <i class="fas fa-square"></i> Alpha
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
