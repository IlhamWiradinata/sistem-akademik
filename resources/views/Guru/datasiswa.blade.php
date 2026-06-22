@extends('Layouts.LayoutsGuru.app')

@section('title')
<title>Sistem Akademik - Kelas yang Diampu</title>
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-chalkboard-teacher"></i> Kelas yang Diampu
    </h1>
    <span class="badge badge-primary">
        <i class="fas fa-user-tie"></i> {{ $guru->user->name }}
    </span>
</div>

<!-- Info Guru -->
<div class="card border-left-primary shadow mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-10">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">NIP</div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $guru->nip }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Nama Guru</div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $guru->user->name }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Email</div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $guru->user->email ?? '-' }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 text-center">
                <i class="fas fa-chalkboard-teacher fa-3x text-primary" style="opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-filter"></i> Filter Data
        </h6>
    </div>
    <div class="card-body">
        <form action="{{ route('DataSiswa') }}" method="GET" id="filterForm">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold text-dark">Tahun Ajaran</label>
                    <select name="tahun_ajaran" class="form-control" onchange="document.getElementById('filterForm').submit()">
                        @foreach($listTahunAjaran as $tahun)
                            <option value="{{ $tahun }}" {{ $tahunAjaran == $tahun ? 'selected' : '' }}>
                                {{ $tahun }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold text-dark">Mata Pelajaran</label>
                    <select name="mapel_id" class="form-control" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Semua Mata Pelajaran</option>
                        @foreach($mapelGuru as $id => $nama)
                            <option value="{{ $id }}" {{ request('mapel_id') == $id ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold text-dark">Semester</label>
                    <select name="semester" class="form-control" onchange="document.getElementById('filterForm').submit()">
                        <option value="Ganjil" {{ request('semester', $semesterAktif) == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap" {{ request('semester', $semesterAktif) == 'Genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Statistik Kelas -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Kelas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalKelas }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-school fa-2x text-gray-300"></i>
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
                            Total Siswa
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSiswa }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            Tahun Ajaran
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $tahunAjaran }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
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
                            Mata Pelajaran Diampu
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $mapelGuru->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-book fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Kelas -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-list"></i> Daftar Kelas yang Diampu
            <small class="text-muted">(Tahun Ajaran: {{ $tahunAjaran }})</small>
        </h6>
    </div>

    <div class="card-body">
        @if($kelasList->count() > 0)
            <div class="row">
                @foreach($kelasList as $kelas)
                @php
                    // Filter berdasarkan mata pelajaran jika ada filter
                    $mapelFilterId = request('mapel_id');
                    $jadwalKelasGuru = isset($kelas->jadwal_kelas_guru) ? $kelas->jadwal_kelas_guru : collect();

                    if ($mapelFilterId && $jadwalKelasGuru->count() > 0) {
                        $filteredJadwal = $jadwalKelasGuru->where('mapel.id', $mapelFilterId);
                        if ($filteredJadwal->count() === 0) {
                            continue; // Skip kelas ini jika tidak ada jadwal untuk mapel yang difilter
                        }
                    }
                @endphp

                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-left-info shadow-sm">
                        <div class="card-header bg-white py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="m-0 font-weight-bold text-info">
                                    <i class="fas fa-school"></i> {{ $kelas->nama_kelas }}
                                </h5>
                                <div>
                                    <span class="badge badge-info">{{ $kelas->tahun_ajaran }}</span>
                                    <span class="badge badge-secondary">{{ $kelas->siswa_count ?? 0 }} Siswa</span>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <i class="fas fa-user-tie text-primary"></i>
                                        <strong>Wali Kelas:</strong><br>
                                        <span class="text-muted">{{ $kelas->waliKelas->user->name ?? 'Belum ada' }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <i class="fas fa-graduation-cap text-success"></i>
                                        <strong>Jurusan:</strong><br>
                                        <span class="text-muted">{{ $kelas->jurusan->nama_jurusan ?? 'Umum' }}</span>
                                    </p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <p class="mb-2">
                                        <i class="fas fa-book text-warning"></i>
                                        <strong>Mata Pelajaran yang Diampu di Kelas Ini:</strong>
                                        <small class="text-muted">(Berdasarkan jadwal)</small>
                                    </p>
                                    <div class="mb-3">
                                        @php
                                            $mapelDiampu = collect();
                                            if (isset($kelas->jadwal_kelas_guru) && $kelas->jadwal_kelas_guru->count() > 0) {
                                                $mapelDiampu = $kelas->jadwal_kelas_guru
                                                    ->pluck('mapel.nama_mapel')
                                                    ->unique();
                                            }
                                        @endphp

                                        @if($mapelDiampu->count() > 0)
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($mapelDiampu as $mapel)
                                                    <span class="badge
                                                        @if($guru->mapel && $mapel == $guru->mapel->nama_mapel)
                                                            badge-success
                                                        @else
                                                            badge-secondary
                                                        @endif
                                                        mb-1">
                                                        {{ $mapel }}
                                                        @if($guru->mapel && $mapel == $guru->mapel->nama_mapel)
                                                            <i class="fas fa-star ml-1" style="font-size: 0.7em;"></i>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                            @if($guru->mapel && !$mapelDiampu->contains($guru->mapel->nama_mapel))
                                                <div class="mt-2">
                                                    <small class="text-danger">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                        Mata pelajaran utama ({{ $guru->mapel->nama_mapel }}) tidak diampu di kelas ini
                                                    </small>
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted">Tidak ada jadwal mengajar di kelas ini</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Jadwal Mengajar di Kelas Ini -->
                            <div class="mb-3">
                                <p class="mb-2">
                                    <i class="fas fa-clock text-info"></i>
                                    <strong>Jadwal Mengajar:</strong>
                                </p>
                                @php
                                    $jadwalKelas = collect();
                                    if (isset($kelas->jadwal_kelas_guru) && $kelas->jadwal_kelas_guru->count() > 0) {
                                        $jadwalKelas = $kelas->jadwal_kelas_guru->sortBy(function($item) {
                                            $hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                            return array_search($item->hari, $hariOrder);
                                        });

                                        // Filter by mapel jika ada
                                        if ($mapelFilterId) {
                                            $jadwalKelas = $jadwalKelas->where('mapel.id', $mapelFilterId);
                                        }
                                    }
                                @endphp

                                @if($jadwalKelas->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless">
                                            <thead>
                                                <tr class="bg-light">
                                                    <th width="30%">Hari</th>
                                                    <th width="40%">Jam</th>
                                                    <th width="30%" class="text-right">Mapel</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($jadwalKelas as $jadwal)
                                                <tr>
                                                    <td>
                                                        <span class="badge badge-light">{{ $jadwal->hari ?? '-' }}</span>
                                                    </td>
                                                    <td>
                                                        <small>{{ $jadwal->jam_mulai ?? '-' }} - {{ $jadwal->jam_selesai ?? '-' }}</small>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="badge
                                                            @if($guru->mapel && $jadwal->mapel && $jadwal->mapel->nama_mapel == $guru->mapel->nama_mapel)
                                                                badge-success
                                                            @else
                                                                badge-primary
                                                            @endif
                                                            ">
                                                            {{ $jadwal->mapel->nama_mapel ?? '-' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted mb-0">
                                        @if($mapelFilterId)
                                            Tidak ada jadwal untuk mata pelajaran ini
                                        @else
                                            Belum ada jadwal mengajar
                                        @endif
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="card-footer bg-white border-top-0">
                            <div class="row">
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-info btn-sm btn-block"
                                            onclick="showSiswaModal({{ $kelas->id }}, '{{ $kelas->nama_kelas }}')">
                                        <i class="fas fa-users"></i> Lihat Siswa
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('DataNilai') }}?kelas_id={{ $kelas->id }}&tahun_ajaran={{ $tahunAjaran }}&mapel_id={{ $mapelFilterId ?? '' }}"
                                       class="btn btn-success btn-sm btn-block">
                                        <i class="fas fa-plus"></i> Nilai
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('DataKehadiran') }}?kelas_id={{ $kelas->id }}&tahun_ajaran={{ $tahunAjaran }}&mapel_id={{ $mapelFilterId ?? '' }}"
                                       class="btn btn-warning btn-sm btn-block">
                                        <i class="fas fa-plus"></i> Kehadiran
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($kelasList->count() == 0 && $mapelFilterId)
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">Tidak Ditemukan Kelas</h5>
                    <p class="text-muted mb-4">Tidak ada kelas yang Anda ampu untuk mata pelajaran yang dipilih</p>
                    <a href="{{ route('DataSiswa') }}" class="btn btn-primary">
                        <i class="fas fa-eye"></i> Lihat Semua Kelas
                    </a>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-chalkboard fa-3x text-gray-300 mb-3"></i>
                <h5 class="text-gray-600">Belum Ada Kelas yang Diampu</h5>
                <p class="text-muted mb-4">Anda belum ditugaskan untuk mengajar di kelas manapun pada tahun ajaran {{ $tahunAjaran }}</p>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Informasi:</strong> Hubungi administrator untuk penugasan mengajar
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal for Students List -->
<div class="modal fade" id="siswaModal" tabindex="-1" role="dialog" aria-labelledby="siswaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="siswaModalLabel">
                    <i class="fas fa-users"></i> Daftar Siswa - <span id="modalKelasNama"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="siswaTable">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">NISN</th>
                                <th width="25%">Nama Siswa</th>
                                <th width="20%">Jenis Kelamin</th>
                                <th width="20%">Kontak</th>
                                <th width="15%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan dimuat via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showSiswaModal(kelasId, kelasNama) {
    $('#modalKelasNama').text(kelasNama);

    // Tampilkan modal
    $('#siswaModal').modal('show');

    // Kosongkan tabel
    $('#siswaTable tbody').html('<tr><td colspan="6" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>');

    // Ambil data via AJAX
    $.ajax({
        url: '/guru/datasiswa/' + kelasId + '/siswa',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data.length > 0) {
                let html = '';
                $.each(response.data, function(index, siswa) {
                    html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${siswa.nisn}</td>
                        <td>${siswa.nama}</td>
                        <td>${siswa.jenis_kelamin}</td>
                        <td>${siswa.kontak || '-'}</td>
                        <td>
                            <span class="badge ${siswa.status === 'Aktif' ? 'badge-success' : 'badge-secondary'}">
                                ${siswa.status}
                            </span>
                        </td>
                    </tr>`;
                });
                $('#siswaTable tbody').html(html);
            } else {
                $('#siswaTable tbody').html('<tr><td colspan="6" class="text-center text-muted">Tidak ada data siswa</td></tr>');
            }
        },
        error: function() {
            $('#siswaTable tbody').html('<tr><td colspan="6" class="text-center text-danger">Gagal memuat data</td></tr>');
        }
    });
}
</script>
@endpush
