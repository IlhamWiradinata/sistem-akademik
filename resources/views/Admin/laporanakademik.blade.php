@extends('layouts.layoutsadmin.app')

@section('title')
<title>Sistem Akademik - Laporan Akademik</title>
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-file-alt"></i> Laporan Akademik Siswa
    </h1>
    <span class="badge badge-primary badge-lg">
        <i class="fas fa-user-shield"></i> Administrator
    </span>
</div>

<!-- Filter Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-filter"></i> Filter Kelas & Semester
        </h6>
    </div>
    <div class="card-body">
        <form action="{{ route('LaporanAkademikAdmin') }}" method="GET" class="row align-items-end">

            <!-- Tahun Ajaran -->
            <div class="col-md-3 mb-3">
                <label class="font-weight-bold mb-2">
                    <i class="fas fa-calendar"></i> Tahun Ajaran
                </label>
                <select name="tahun_ajaran" class="form-control" id="tahun_ajaran"  required>
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
                <label class="font-weight-bold mb-2">
                    <i class="fas fa-school"></i> Pilih Kelas
                </label>
                <select name="kelas_id" id="kelas_id" class="form-control">
                    <option value="">-- Pilih Kelas --</option>

                    @foreach($kelasList as $kelasItem)
                        <option value="{{ $kelasItem->id }}"
                            {{ $kelasId == $kelasItem->id ? 'selected' : '' }}>
                            {{ $kelasItem->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Semester -->
            <div class="col-md-3 mb-3">
                <label class="font-weight-bold mb-2">
                    <i class="fas fa-calendar-alt"></i> Semester
                </label>
                <select name="semester" class="form-control" required>
                    <option value="">-- Pilih Semester --</option>
                    <option value="Ganjil" {{ $semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                    <option value="Genap" {{ $semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                </select>
            </div>

            <!-- Button -->
            <div class="col-md-2 mb-3">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
            </div>

        </form>

        @if($kelasId && $semester)
        <div class="alert alert-info mt-3 mb-0">
            <i class="fas fa-info-circle"></i>
            Menampilkan data kelas: <strong>{{ $kelasList->find($kelasId)->nama_kelas ?? '-' }}</strong> |
            Semester: <strong>{{ $semester }}</strong>
        </div>
        @endif
    </div>
</div>

@if($kelasId && $semester)
<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Siswa</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $siswaList->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Laporan Selesai</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $siswaList->where('status_laporan', 'selesai')->count() }}
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Belum Selesai</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $siswaList->where('status_laporan', 'belum')->count() }}
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Progress</div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                    {{ $siswaList->count() > 0
                                        ? round(($siswaList->where('status_laporan', 'selesai')->count() / $siswaList->count()) * 100)
                                        : 0
                                    }}%
                                </div>
                            </div>
                            <div class="col">
                                <div class="progress progress-sm mr-2">
                                    <div class="progress-bar bg-info" role="progressbar"
                                         style="width: {{ $siswaList->count() > 0
                                            ? round(($siswaList->where('status_laporan', 'selesai')->count() / $siswaList->count()) * 100)
                                            : 0
                                        }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Siswa -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-list"></i> Daftar Siswa - {{ $kelasList->find($kelasId)->nama_kelas ?? '' }}
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr class="text-center">
                        <th width="5%">No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Jurusan</th>
                        <th width="10%">Status</th>
                        <th width="25%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswaList as $index => $siswa)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">
                            <span class="badge badge-secondary">{{ $siswa->nis }}</span>
                        </td>
                        <td>
                            <i class="fas fa-user-graduate text-primary"></i>
                            <strong>{{ $siswa->user->name }}</strong>
                        </td>
                        <td class="text-center">{{ $siswa->jurusan->nama_jurusan }}</td>
                        <td class="text-center">
                            @if($siswa->status_laporan == 'selesai')
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> Selesai
                                </span>
                            @else
                                <span class="badge badge-warning" title="
                                    {{ $siswa->laporanAkademik ? 'Laporan belum diisi' : '' }}
                                    {{ $siswa->nilai->isEmpty() ? ' | Nilai belum ada' : '' }}
                                    {{ $siswa->kehadiran->isEmpty() ? ' | Kehadiran belum ada' : '' }}">
                                    <i class="fas fa-clock"></i> Belum
                                </span>
                            @endif

                        </td>
                        <td class="text-center">
                            <!-- Lihat Detail Button -->
                            <button class="btn btn-info btn-sm"
                                    data-toggle="modal"
                                    data-target="#detailModal{{ $siswa->id }}"
                                    title="Lihat Detail">
                                <i class="fas fa-eye"></i> Detail
                            </button>

                            <!-- Input/Edit Laporan Button -->
                            @if($siswa->laporanAkademik)
                                <button class="btn btn-warning btn-sm"
                                        data-toggle="modal"
                                        data-target="#laporanModal{{ $siswa->id }}"
                                        title="Edit Laporan">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            @else
                                <button class="btn btn-primary btn-sm"
                                        data-toggle="modal"
                                        data-target="#laporanModal{{ $siswa->id }}"
                                        title="Input Laporan">
                                    <i class="fas fa-plus"></i> Input
                                </button>
                            @endif

                            <!-- Download PDF Button -->
                            <a href="{{ route('laporan.unduh') }}?nis={{ $siswa->nis }}&semester={{ $semester }}&kelas_id={{ $kelasId }}&tahun_ajaran={{ $tahunAjaran }}"
                            class="btn btn-danger btn-sm"
                            target="_blank">
                                <i class="fas fa-file-pdf"></i> PDF
                            </a>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <p class="mb-0">Tidak ada siswa di kelas ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @foreach ($siswaList as $siswa)
                    <!-- Modal Detail Siswa -->
                    <div class="modal fade" id="detailModal{{ $siswa->id }}" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-info text-white">
                                    <h5 class="modal-title">
                                        <i class="fas fa-clipboard-list"></i> Detail Laporan - {{ $siswa->user->name }}
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    @php
                                        $nilai = $siswa->nilai->where('semester', $semester);
                                        $kehadiranSiswa = $siswa->kehadiran->where('semester', $semester)->where('tahun_ajaran', $tahunAjaran);
                                        $totalMapel = $nilai->count();
                                        $rataRata = $nilai->avg('rata_rata') ?? 0;

                                        $rekapKehadiran = (object)[
                                            'hadir' => $kehadiranSiswa->where('status', 'hadir')->count(),
                                            'izin'  => $kehadiranSiswa->where('status', 'izin')->count(),
                                            'sakit' => $kehadiranSiswa->where('status', 'sakit')->count(),
                                            'alpha' => $kehadiranSiswa->where('status', 'alpa')->count(),
                                        ];

                                        $totalKehadiran = $rekapKehadiran->hadir + $rekapKehadiran->izin + $rekapKehadiran->sakit + $rekapKehadiran->alpha;
                                        $persentaseHadir = $totalKehadiran > 0 ? ($rekapKehadiran->hadir / $totalKehadiran * 100) : 0;

                                        $laporanSiswa = $siswa->laporanAkademik; // sudah eager-loaded sesuai kelas_id & semester
                                    @endphp

                                    <!-- Identitas Siswa -->
                                    <div class="card border-left-primary mb-3">
                                        <div class="card-body">
                                            <h6 class="font-weight-bold text-primary mb-3">
                                                <i class="fas fa-user-graduate"></i> Identitas Siswa
                                            </h6>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <small class="text-muted">NIS</small>
                                                    <div class="font-weight-bold">{{ $siswa->nis }}</div>
                                                </div>
                                                <div class="col-md-3">
                                                    <small class="text-muted">Nama Lengkap</small>
                                                    <div class="font-weight-bold">{{ $siswa->user->name }}</div>
                                                </div>
                                                <div class="col-md-3">
                                                    <small class="text-muted">Kelas</small>
                                                    <div class="font-weight-bold">{{ $kelasAktif->nama_kelas ?? '-' }}</div>
                                                </div>
                                                <div class="col-md-3">
                                                    <small class="text-muted">Jurusan</small>
                                                    <div class="font-weight-bold">{{ $siswa->jurusan->nama_jurusan }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Statistics -->
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <div class="card border-left-primary h-100">
                                                <div class="card-body py-2">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Mata Pelajaran</div>
                                                    <div class="h5 mb-0 font-weight-bold">{{ $totalMapel }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card border-left-success h-100">
                                                <div class="card-body py-2">
                                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Rata-rata</div>
                                                    <div class="h5 mb-0 font-weight-bold">{{ number_format($rataRata, 2) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card border-left-info h-100">
                                                <div class="card-body py-2">
                                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Kehadiran</div>
                                                    <div class="h5 mb-0 font-weight-bold">{{ $rekapKehadiran->hadir ?? 0 }} Hari</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card border-left-warning h-100">
                                                <div class="card-body py-2">
                                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">% Hadir</div>
                                                    <div class="h5 mb-0 font-weight-bold">{{ number_format($persentaseHadir, 1) }}%</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tabs -->
                                    <ul class="nav nav-tabs" id="detailTab{{ $siswa->id }}" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-toggle="tab" href="#nilai{{ $siswa->id }}">
                                                <i class="fas fa-graduation-cap"></i> Rekap Nilai
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#kehadiran{{ $siswa->id }}">
                                                <i class="fas fa-calendar-check"></i> Rekap Kehadiran
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#laporan{{ $siswa->id }}">
                                                <i class="fas fa-file-alt"></i> Laporan Siswa
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <!-- Tab Nilai -->
                                        <div class="tab-pane fade" id="nilai{{ $siswa->id }}">
                                            <div class="table-responsive mt-3">
                                                <table class="table table-bordered table-sm">
                                                    <thead class="bg-light">
                                                        <tr class="text-center">
                                                            <th width="5%">No</th>
                                                            <th>Mata Pelajaran</th>
                                                            <th width="8%">Tugas</th>
                                                            <th width="8%">Praktikum</th>
                                                            <th width="8%">UTS</th>
                                                            <th width="8%">UAS</th>
                                                            <th width="8%">Sikap</th>
                                                            <th width="10%">Rata-rata</th>
                                                            <th width="8%">Grade</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($nilai as $idx => $item)
                                                        <tr>
                                                            <td class="text-center">{{ $idx + 1 }}</td>
                                                            <td>{{ $item->mapel->nama_mapel }}</td>
                                                            <td class="text-center">{{ $item->nilai_tugas }}</td>
                                                            <td class="text-center">{{ $item->nilai_praktikum }}</td>
                                                            <td class="text-center">{{ $item->nilai_uts }}</td>
                                                            <td class="text-center">{{ $item->nilai_uas }}</td>
                                                            <td class="text-center">
                                                                <span class="badge badge-{{ $item->sikap == 'A' ? 'success' : ($item->sikap == 'B' ? 'info' : 'warning') }}">
                                                                    {{ $item->sikap }}
                                                                </span>
                                                            </td>
                                                            <td class="text-center">
                                                                <strong>{{ number_format($item->rata_rata, 2) }}</strong>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge badge-{{ $item->grade == 'A' ? 'success' : ($item->grade == 'B' ? 'info' : 'warning') }}">
                                                                    {{ $item->grade }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="9" class="text-center text-muted">Tidak ada data nilai</td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Tab Kehadiran -->
                                        <div class="tab-pane fade" id="kehadiran{{ $siswa->id }}">
                                            <div class="row mt-3">
                                                <div class="col-md-3">
                                                    <div class="card border-left-success text-center">
                                                        <div class="card-body">
                                                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                                            <div class="text-success font-weight-bold">HADIR</div>
                                                            <div class="h3">{{ $rekapKehadiran->hadir ?? 0 }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card border-left-info text-center">
                                                        <div class="card-body">
                                                            <i class="fas fa-file-alt fa-2x text-info mb-2"></i>
                                                            <div class="text-info font-weight-bold">IZIN</div>
                                                            <div class="h3">{{ $rekapKehadiran->izin ?? 0 }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card border-left-warning text-center">
                                                        <div class="card-body">
                                                            <i class="fas fa-notes-medical fa-2x text-warning mb-2"></i>
                                                            <div class="text-warning font-weight-bold">SAKIT</div>
                                                            <div class="h3">{{ $rekapKehadiran->sakit ?? 0 }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card border-left-danger text-center">
                                                        <div class="card-body">
                                                            <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                                                            <div class="text-danger font-weight-bold">ALPHA</div>
                                                            <div class="h3">{{ $rekapKehadiran->alpha ?? 0 }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tab Laporan Akademik -->
                                        <div class="tab-pane fade" id="laporan{{ $siswa->id }}">

                                            <div class="row mt-3">

                                                <!-- Catatan Akademik -->
                                                <div class="col-md-6">
                                                    <div class="card border-left-primary h-100">
                                                        <div class="card-body">
                                                            <h6 class="font-weight-bold text-primary">
                                                                <i class="fas fa-book-reader"></i> Catatan Akademik
                                                            </h6>
                                                            <p class="mt-2">
                                                                {{ $laporanSiswa->catatan_akademik ?? 'Belum ada catatan akademik.' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Catatan Sikap -->
                                                <div class="col-md-6">
                                                    <div class="card border-left-success h-100">
                                                        <div class="card-body">
                                                            <h6 class="font-weight-bold text-success">
                                                                <i class="fas fa-user-check"></i> Catatan Sikap
                                                            </h6>
                                                            <p class="mt-2">
                                                                {{ $laporanSiswa->catatan_sikap ?? 'Belum ada catatan sikap.' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="row mt-3">

                                                <!-- Kesimpulan -->
                                                <div class="col-md-6">
                                                    <div class="card border-left-info h-100">
                                                        <div class="card-body">
                                                            <h6 class="font-weight-bold text-info">
                                                                <i class="fas fa-check-circle"></i> Kesimpulan
                                                            </h6>
                                                            <p class="mt-2">
                                                                {{ $laporanSiswa->kesimpulan ?? 'Belum ada kesimpulan.' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Rekomendasi -->
                                                <div class="col-md-6">
                                                    <div class="card border-left-warning h-100">
                                                        <div class="card-body">
                                                            <h6 class="font-weight-bold text-warning">
                                                                <i class="fas fa-lightbulb"></i> Rekomendasi
                                                            </h6>
                                                            <p class="mt-2">
                                                                {{ $laporanSiswa->rekomendasi ?? 'Belum ada rekomendasi.' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Modal Input/Edit Laporan -->
                    <div class="modal fade" id="laporanModal{{ $siswa->id }}" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <form action="{{ route('laporan.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="tahun_ajaran" value="{{ request('tahun_ajaran') }}">
                                    <input type="hidden" name="nis" value="{{ $siswa->nis }}">
                                    <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
                                    <input type="hidden" name="semester" value="{{ $semester }}">

                                    <div class="modal-header bg-{{ $laporanSiswa ? 'warning' : 'primary' }} text-white">
                                        <h5 class="modal-title">
                                            <i class="fas fa-{{ $laporanSiswa ? 'edit' : 'plus' }}"></i>
                                            {{ $laporanSiswa ? 'Edit' : 'Input' }} Laporan - {{ $siswa->user->name }}
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            Isikan catatan, kesimpulan, dan rekomendasi untuk siswa <strong>{{ $siswa->user->name }}</strong>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold">
                                                <i class="fas fa-book-reader text-primary"></i> Catatan Akademik
                                            </label>
                                            <textarea name="catatan_akademik" class="form-control" rows="4" placeholder="...">{{ $laporanSiswa->catatan_akademik ?? '' }}</textarea>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold">
                                                <i class="fas fa-user-check text-success"></i> Catatan Sikap
                                            </label>
                                            <textarea name="catatan_sikap" class="form-control" rows="4" placeholder="...">{{ $laporanSiswa->catatan_sikap ?? '' }}</textarea>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="font-weight-bold">
                                                <i class="fas fa-check-circle text-info"></i> Kesimpulan
                                            </label>
                                            <textarea name="kesimpulan" class="form-control" rows="3" placeholder="...">{{ $laporanSiswa->kesimpulan ?? '' }}</textarea>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold">
                                                <i class="fas fa-lightbulb text-warning"></i> Rekomendasi
                                            </label>
                                            <textarea name="rekomendasi" class="form-control" rows="3" placeholder="...">{{ $laporanSiswa->rekomendasi ?? '' }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            <i class="fas fa-times"></i> Batal
                                        </button>
                                        <button type="submit" class="btn btn-{{ $siswa->laporanAkademik ? 'warning' : 'primary' }}">
                                            <i class="fas fa-save"></i> Simpan Laporan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

            @endforeach
        </div>
    </div>
</div>
@else

<!-- Empty State -->
<div class="card shadow mb-4">
    <div class="card-body text-center py-5">
        <i class="fas fa-filter fa-4x text-gray-300 mb-3"></i>
        <h5 class="text-gray-600">Silakan pilih Kelas dan Semester</h5>
        <p class="text-muted">Gunakan filter di atas untuk menampilkan daftar siswa</p>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
document.getElementById('tahun_ajaran').addEventListener('change', function () {
    const tahunAjaran = this.value;
    const kelasSelect = document.getElementById('kelas_id');

    kelasSelect.innerHTML = '<option value="">Memuat kelas...</option>';

    if (!tahunAjaran) {
        kelasSelect.innerHTML = '<option value="">-- Pilih Kelas --</option>';
        return;
    }

    fetch(`{{ route('LaporanAkademikAdmin') }}?tahun_ajaran=${tahunAjaran}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        kelasSelect.innerHTML = '<option value="">-- Pilih Kelas --</option>';
        data.forEach(kelas => {
            kelasSelect.innerHTML += `
                <option value="${kelas.id}">${kelas.nama_kelas}</option>
            `;
        });
    })
    .catch(() => {
        kelasSelect.innerHTML = '<option value="">Gagal memuat kelas</option>';
    });
});

$(document).on('hidden.bs.modal', '.modal', function () {
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
});
</script>
@endpush
