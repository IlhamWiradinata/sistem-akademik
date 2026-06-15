@extends('layouts.layoutssiswa.app')

@section('title')
<title>Sistem Akademik - Laporan Akademik Siswa</title>
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-file-alt"></i> Laporan Akademik
    </h1>
    <a href="{{ route('laporanSiswa.pdf', ['nis' => $profile->nis, 'semester' => request('semester', $semesterAktif)]) }}"
   class="btn btn-danger btn-icon-split" target="_blank">

        <span class="icon text-white-50">
            <i class="fas fa-file-pdf"></i>
        </span>
        <span class="text">Unduh PDF</span>
    </a>
</div>

<!-- Student Profile Card -->
<div class="card border-left-primary shadow mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-10">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">NIS</div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $profile->nis }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Nama Lengkap</div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $user->name }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Kelas</div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $kelasTerakhir->nama_kelas }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Wali Kelas</div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $waliKelas }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 text-center">
                <i class="fas fa-user-graduate fa-3x text-primary" style="opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-filter"></i> Filter Laporan Akademik
        </h6>
    </div>
    <div class="card-body">
        <form action="{{ route('LaporanAkademikSiswa') }}" method="GET" class="row">
            <div class="col-md-5">
                <label class="font-weight-bold small mb-2">
                    <i class="fas fa-school"></i> Kelas
                </label>
                <select name="kelas_id" class="form-control">
                    @foreach($kelasList as $kelasItem)
                        <option value="{{ $kelasItem->id }}"
                            {{ $kelasItem->id == $kelasId ? 'selected' : '' }}>
                            {{ $kelasItem->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-5">
                <label class="font-weight-bold small mb-2">
                    <i class="fas fa-calendar-alt"></i> Semester
                </label>
                <select name="semester" class="form-control">
                    <option value="Ganjil" {{ request('semester') == 'Ganjil' ? 'selected' : '' }}>
                        Semester Ganjil
                    </option>
                    <option value="Genap" {{ request('semester') == 'Genap' ? 'selected' : '' }}>
                        Semester Genap
                    </option>
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

<!-- Statistics Cards -->
<div class="row">
    @php
        $totalMapel = $nilai->count();
        $rataRata = $nilai->avg('rata_rata') ?? 0;
        $totalKehadiran = $kehadiran->hadir + $kehadiran->izin + $kehadiran->sakit + $kehadiran->alpha;
        $persentaseHadir = $totalKehadiran > 0 ? ($kehadiran->hadir / $totalKehadiran) * 100 : 0;
    @endphp

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Mata Pelajaran</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalMapel }}</div>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Rata-rata Nilai</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($rataRata, 2) }}</div>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Kehadiran</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $kehadiran->hadir }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Persentase Hadir</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($persentaseHadir, 1) }}%</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rekap Nilai -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-graduation-cap"></i> Rekapitulasi Nilai
        </h6>
        <span class="badge badge-primary">{{ $totalMapel }} Mata Pelajaran</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-primary text-white">
                    <tr class="text-center">
                        <th width="5%">No</th>
                        <th class="text-left" width="25%">Mata Pelajaran</th>
                        <th width="10%">Tugas</th>
                        <th width="10%">Praktikum</th>
                        <th width="10%">UTS</th>
                        <th width="10%">UAS</th>
                        <th width="10%">Sikap</th>
                        <th width="10%">Rata-rata</th>
                        <th width="10%">Grade</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nilai as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td><strong>{{ $item->mapel->nama_mapel }}</strong></td>
                        <td class="text-center">
                            <span class="badge badge-light">{{ $item->nilai_tugas }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light">{{ $item->nilai_praktikum }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light">{{ $item->nilai_uts }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light">{{ $item->nilai_uas }}</span>
                        </td>
                        <td class="text-center">
                            @if($item->sikap == 'A')
                                <span class="badge badge-success">{{ $item->sikap }}</span>
                            @elseif($item->sikap == 'B')
                                <span class="badge badge-info">{{ $item->sikap }}</span>
                            @else
                                <span class="badge badge-warning">{{ $item->sikap }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <strong class="text-{{ $item->rata_rata >= 85 ? 'success' : ($item->rata_rata >= 75 ? 'info' : 'warning') }}">
                                {{ number_format($item->rata_rata, 2) }}
                            </strong>
                        </td>
                        <td class="text-center">
                            @if($item->grade == 'A')
                                <span class="badge badge-success px-3 py-2">{{ $item->grade }}</span>
                            @elseif($item->grade == 'B')
                                <span class="badge badge-info px-3 py-2">{{ $item->grade }}</span>
                            @elseif($item->grade == 'C')
                                <span class="badge badge-warning px-3 py-2">{{ $item->grade }}</span>
                            @else
                                <span class="badge badge-danger px-3 py-2">{{ $item->grade }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>Tidak ada data nilai</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Rekap Kehadiran -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-calendar-check"></i> Rekapitulasi Kehadiran
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card border-left-success h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <div class="text-success font-weight-bold">HADIR</div>
                        <div class="h3 mb-0">{{ $kehadiran->hadir }}</div>
                        <small class="text-muted">Hari</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-left-info h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-file-alt fa-2x text-info mb-2"></i>
                        <div class="text-info font-weight-bold">IZIN</div>
                        <div class="h3 mb-0">{{ $kehadiran->izin }}</div>
                        <small class="text-muted">Hari</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-left-warning h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-notes-medical fa-2x text-warning mb-2"></i>
                        <div class="text-warning font-weight-bold">SAKIT</div>
                        <div class="h3 mb-0">{{ $kehadiran->sakit }}</div>
                        <small class="text-muted">Hari</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-left-danger h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                        <div class="text-danger font-weight-bold">ALPHA</div>
                        <div class="h3 mb-0">{{ $kehadiran->alpha }}</div>
                        <small class="text-muted">Hari</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Kehadiran -->
        <div class="row mt-3">
            <div class="col-12">
                <h6 class="font-weight-bold mb-2">
                    <i class="fas fa-chart-pie"></i> Persentase Kehadiran
                </h6>
                <div class="progress" style="height: 30px;">
                    <div class="progress-bar bg-{{ $persentaseHadir >= 80 ? 'success' : ($persentaseHadir >= 60 ? 'warning' : 'danger') }}"
                         role="progressbar"
                         style="width: {{ $persentaseHadir }}%">
                        <strong>{{ number_format($persentaseHadir, 1) }}%</strong>
                    </div>
                </div>
                <div class="text-center mt-2">
                    <small class="text-muted">
                        Total: {{ $totalKehadiran }} hari |
                        Hadir: {{ $kehadiran->hadir }} hari |
                        Tidak Hadir: {{ $kehadiran->izin + $kehadiran->sakit + $kehadiran->alpha }} hari
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Catatan Akademik -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-book-reader"></i> Catatan Akademik
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="font-weight-bold small text-primary mb-2">
                        <i class="fas fa-clipboard-list"></i> Prestasi & Pencapaian
                    </label>
                    <div class="alert alert-light border-left border-primary">
                        {{ $laporanakademik->catatan_akademik ?? 'Belum ada catatan akademik' }}
                    </div>
                </div>
                <div>
                    <label class="font-weight-bold small text-success mb-2">
                        <i class="fas fa-user-check"></i> Sikap & Perilaku
                    </label>
                    <div class="alert alert-light border-left border-success">
                        {{ $laporanakademik->catatan_sikap ?? 'Belum ada catatan sikap' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-lightbulb"></i> Kesimpulan & Rekomendasi
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="font-weight-bold small text-info mb-2">
                        <i class="fas fa-check-circle"></i> Kesimpulan
                    </label>
                    <div class="alert alert-light border-left border-info">
                        {{ $laporanakademik->kesimpulan ?? 'Belum ada kesimpulan' }}
                    </div>
                </div>
                <div>
                    <label class="font-weight-bold small text-warning mb-2">
                        <i class="fas fa-star"></i> Rekomendasi
                    </label>
                    <div class="alert alert-light border-left border-warning">
                        {{ $laporanakademik->rekomendasi ?? 'Belum ada rekomendasi' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer Info -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i>
                    Laporan ini dibuat secara otomatis oleh sistem
                </small>
            </div>
            <div class="col-md-6 text-right">
                <small class="text-muted">
                    <i class="far fa-calendar-alt"></i>
                    Tanggal: {{ date('d F Y') }}
                </small>
            </div>
        </div>
    </div>
</div>

@endsection
