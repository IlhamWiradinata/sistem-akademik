@extends('layouts.layoutsguru.app')

@section('title')
<title>Sistem Akademik - Dashboard Guru</title>
@endsection

@section('content')
@php
    $stats = $statistik ?? [];
    $totalKelas = $stats['total_kelas'] ?? 0;
    $totalSiswa = $stats['total_siswa'] ?? 0;
    $totalMapel = $stats['total_mapel'] ?? 0;
    $totalNilai = $stats['total_nilai'] ?? 0;
    $hadirHariIni = $stats['kehadiran_hari_ini'] ?? [];
    $hadir = $hadirHariIni['hadir'] ?? 0;
    $izin   = $hadirHariIni['izin'] ?? 0;
    $sakit  = $hadirHariIni['sakit'] ?? 0;
    $alpa   = $hadirHariIni['alpa'] ?? 0;
    $totalHariIni = $hadir + $izin + $sakit + $alpa;
    $kelasDanMapel = $stats['kelas_dan_mapel'] ?? [];
    $semesterAktif = $stats['semester_aktif'] ?? 'Genap';
@endphp

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-tachometer-alt"></i> Dashboard Guru</h1>
    <span class="badge badge-primary badge-lg"><i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
</div>

<!-- Welcome Card -->
<div class="card border-left-primary shadow mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Selamat Datang</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $guru->user->name }}</div>
                <div class="text-muted small mt-1">
                    <i class="fas fa-id-card"></i> NIP: {{ $guru->nip }} |
                    <i class="fas fa-envelope"></i> {{ $guru->user->email ?? '-' }} |
                    <span class="badge badge-success">Aktif</span>
                </div>
            </div>
            <div class="col-auto"><i class="fas fa-user-tie fa-3x text-primary" style="opacity:0.3"></i></div>
        </div>
    </div>
</div>

<!-- Statistics Cards Row -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4"><div class="card border-left-primary shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Kelas</div><div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalKelas }}</div><div class="text-muted small mt-1">Kelas Diampu</div></div><div class="col-auto"><i class="fas fa-school fa-2x text-gray-300"></i></div></div></div></div></div>
    <div class="col-xl-3 col-md-6 mb-4"><div class="card border-left-success shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Siswa</div><div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSiswa }}</div><div class="text-muted small mt-1">Siswa Diajar</div></div><div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div></div></div></div></div>
    <div class="col-xl-3 col-md-6 mb-4"><div class="card border-left-info shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-info text-uppercase mb-1">Mata Pelajaran</div><div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalMapel }}</div><div class="text-muted small mt-1">Total Mapel</div></div><div class="col-auto"><i class="fas fa-book fa-2x text-gray-300"></i></div></div></div></div></div>
    <div class="col-xl-3 col-md-6 mb-4"><div class="card border-left-warning shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Nilai Tersimpan</div><div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalNilai }}</div><div class="text-muted small mt-1">Data Penilaian</div></div><div class="col-auto"><i class="fas fa-graduation-cap fa-2x text-gray-300"></i></div></div></div></div></div>
</div>

<!-- Baris Pertama: Kehadiran Hari Ini & Kehadiran Bulan Ini -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-clipboard-check"></i> Kehadiran Hari Ini</h6></div>
            <div class="card-body">
                <div class="row text-center mb-3">
                    <div class="col-3 border-right"><div class="small text-muted mb-1">Hadir</div><div class="h4 mb-0 font-weight-bold text-success">{{ $hadir }}</div></div>
                    <div class="col-3 border-right"><div class="small text-muted mb-1">Izin</div><div class="h4 mb-0 font-weight-bold text-info">{{ $izin }}</div></div>
                    <div class="col-3 border-right"><div class="small text-muted mb-1">Sakit</div><div class="h4 mb-0 font-weight-bold text-warning">{{ $sakit }}</div></div>
                    <div class="col-3"><div class="small text-muted mb-1">Alpa</div><div class="h4 mb-0 font-weight-bold text-danger">{{ $alpa }}</div></div>
                </div>
                @php $persenHari = $totalHariIni > 0 ? round(($hadir / $totalHariIni)*100) : 0; @endphp
                <div class="progress mb-3"><div class="progress-bar bg-success" style="width: {{ $persenHari }}%"></div></div>
                <div class="text-center small text-muted">Kehadiran hari ini: {{ $persenHari }}%</div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-calendar-month"></i> Kehadiran Bulan Ini</h6></div>
            <div class="card-body text-center">
                @php $persenBulan = $stats['persentase_kehadiran_bulan_ini'] ?? 0; @endphp
                <div class="h1 font-weight-bold text-{{ $persenBulan >= 85 ? 'success' : ($persenBulan >= 60 ? 'warning' : 'danger') }}">{{ $persenBulan }}%</div>
                <div class="progress mb-2" style="height:10px"><div class="progress-bar bg-{{ $persenBulan >= 85 ? 'success' : ($persenBulan >= 60 ? 'warning' : 'danger') }}" style="width: {{ $persenBulan }}%"></div></div>
                <div class="small text-muted">Target: 85% | Actual: {{ $persenBulan }}%</div>
            </div>
        </div>
    </div>
</div>

<!-- Baris Kedua: Rekapitulasi Kehadiran & Distribusi Grade -->
<div class="row">
    <!-- Rekapitulasi Kehadiran -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-bar"></i> Rekapitulasi Kehadiran</h6>
                <div class="dropdown no-arrow">
                    <button class="btn btn-sm btn-link dropdown-toggle" type="button" data-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                    <div class="dropdown-menu dropdown-menu-right p-3" style="min-width:220px">
                        <div class="form-group mb-2"><label class="small">Semester</label>
                            <select id="filter_rekap_semester" class="form-control form-control-sm">
                                <option value="Ganjil" {{ $semesterAktif == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                <option value="Genap" {{ $semesterAktif == 'Genap' ? 'selected' : '' }}>Genap</option>
                            </select>
                        </div>
                        <div class="form-group mb-2"><label class="small">Kelas</label>
                            <select id="filter_rekap_kelas" class="form-control form-control-sm">
                                <option value="">-- Semua Kelas --</option>
                                @foreach($daftarKelasGuru as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-primary btn-sm btn-block mt-2" id="applyRekapFilter">Terapkan</button>
                    </div>
                </div>
            </div>
            <div class="card-body" id="rekapContent">
                <div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div><p class="mt-2">Memuat data...</p></div>
            </div>
        </div>
    </div>

    <!-- Distribusi Grade -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-pie"></i> Distribusi Grade Siswa</h6>
                <div class="dropdown no-arrow">
                    <button class="btn btn-sm btn-link dropdown-toggle" type="button" data-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                    <div class="dropdown-menu dropdown-menu-right p-3" style="min-width:220px">
                        <div class="form-group mb-2"><label class="small">Semester</label>
                            <select id="filter_grade_semester" class="form-control form-control-sm">
                                <option value="Ganjil" {{ $semesterAktif == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                <option value="Genap" {{ $semesterAktif == 'Genap' ? 'selected' : '' }}>Genap</option>
                            </select>
                        </div>
                        <div class="form-group mb-2"><label class="small">Kelas</label>
                            <select id="filter_grade_kelas" class="form-control form-control-sm">
                                <option value="">-- Semua Kelas --</option>
                                @foreach($daftarKelasGuru as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-primary btn-sm btn-block mt-2" id="applyGradeFilter">Terapkan</button>
                    </div>
                </div>
            </div>
            <div class="card-body" id="gradeContent">
                <div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div><p class="mt-2">Memuat data...</p></div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Kelas & Mata Pelajaran dengan Filter Semester (AJAX, tanpa partial) -->
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-school"></i> Daftar Kelas & Mata Pelajaran yang Diampu
                </h6>
                <select id="semesterKelasFilter" class="form-control form-control-sm" style="width: auto;">
                    <option value="Ganjil" {{ ($stats['semester_aktif'] ?? 'Genap') == 'Ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
                    <option value="Genap" {{ ($stats['semester_aktif'] ?? 'Genap') == 'Genap' ? 'selected' : '' }}>Semester Genap</option>
                </select>
            </div>
            <div class="card-body p-0" id="kelasMapelContainer">
                @if(count($kelasDanMapel) > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th class="text-center">Jumlah Siswa</th>
                                <th class="text-center">Nilai Tersimpan</th>
                                <th class="text-center">Rata-rata</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kelasDanMapel as $i => $item)
                            <tr>
                                <td class="text-center">{{ $i+1 }}</td>
                                <td><strong>{{ $item['kelas'] }}</strong></td>
                                <td>{{ $item['mapel'] }}</td>
                                <td class="text-center"><span class="badge badge-primary">{{ $item['jumlah_siswa'] }}</span></td>
                                <td class="text-center"><span class="badge badge-info">{{ $item['nilai_tersimpan'] }}</span></td>
                                <td class="text-center">
                                    <strong class="text-{{ ($item['rata_rata_nilai']>=85)?'success':(($item['rata_rata_nilai']>=75)?'info':'warning') }}">
                                        {{ number_format($item['rata_rata_nilai'],2) }}
                                    </strong>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-gray-300"></i>
                    <p class="mt-2">Belum ada data nilai untuk semester {{ $stats['semester_aktif'] ?? 'Genap' }}</p>
                    <a href="{{ route('DataNilai') }}" class="btn btn-sm btn-primary">Input Nilai</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        function loadRekap(semester, kelasId) {
            $.ajax({
                url: '{{ route("filterData") }}',
                data: { semester: semester, kelas_id: kelasId },
                success: function(res) {
                    let r = res.rekap;
                    let total = r.hadir + r.izin + r.sakit + r.alpa;
                    let persen = total > 0 ? (r.hadir / total * 100).toFixed(1) : 0;
                    $('#rekapContent').html(`
                        <div class="row text-center mb-3">
                            <div class="col-3"><div class="small text-muted">Hadir</div><div class="h5 font-weight-bold text-success">${r.hadir}</div></div>
                            <div class="col-3"><div class="small text-muted">Izin</div><div class="h5 font-weight-bold text-info">${r.izin}</div></div>
                            <div class="col-3"><div class="small text-muted">Sakit</div><div class="h5 font-weight-bold text-warning">${r.sakit}</div></div>
                            <div class="col-3"><div class="small text-muted">Alpa</div><div class="h5 font-weight-bold text-danger">${r.alpa}</div></div>
                        </div>
                        <div class="progress mt-3"><div class="progress-bar bg-success" style="width: ${persen}%"></div></div>
                        <div class="text-center small text-muted mt-2">Total Kehadiran: ${total} | Kehadiran: ${persen}%</div>
                    `);
                },
                error: () => $('#rekapContent').html('<div class="text-center text-danger">Gagal memuat data</div>')
            });
        }

        function loadGrade(semester, kelasId) {
            $.ajax({
                url: '{{ route("filterData") }}',
                data: { semester: semester, kelas_id: kelasId },
                success: function(res) {
                    let g = res.grade;
                    let total = res.totalGrade;
                    let de = (g.D||0)+(g.E||0);
                    if(total > 0) {
                        let html = `
                            <div class="row text-center">
                                <div class="col-3"><div class="small text-muted">Grade A</div><div class="h5 text-success">${g.A}</div></div>
                                <div class="col-3"><div class="small text-muted">Grade B</div><div class="h5 text-info">${g.B}</div></div>
                                <div class="col-3"><div class="small text-muted">Grade C</div><div class="h5 text-warning">${g.C}</div></div>
                                <div class="col-3"><div class="small text-muted">Grade D/E</div><div class="h5 text-danger">${de}</div></div>
                            </div>
                            <div class="progress mt-3" style="height:10px">
                                <div class="progress-bar bg-success" style="width:${(g.A/total)*100}%"></div>
                                <div class="progress-bar bg-info" style="width:${(g.B/total)*100}%"></div>
                                <div class="progress-bar bg-warning" style="width:${(g.C/total)*100}%"></div>
                                <div class="progress-bar bg-danger" style="width:${(de/total)*100}%"></div>
                            </div>
                            <div class="text-center small text-muted mt-2">Total Nilai: ${total}</div>
                        `;
                        $('#gradeContent').html(html);
                    } else {
                        $('#gradeContent').html(`<div class="text-center py-3"><i class="fas fa-chart-pie fa-3x text-gray-300"></i><p class="mt-2">Belum ada data nilai</p></div>`);
                    }
                },
                error: () => $('#gradeContent').html('<div class="text-center text-danger">Gagal memuat data</div>')
            });
        }

        $('#applyRekapFilter').click(function() {
            loadRekap($('#filter_rekap_semester').val(), $('#filter_rekap_kelas').val());
        });
        $('#applyGradeFilter').click(function() {
            loadGrade($('#filter_grade_semester').val(), $('#filter_grade_kelas').val());
        });

        let defaultSemester = '{{ $semesterAktif }}';
        loadRekap(defaultSemester, '');
        loadGrade(defaultSemester, '');
    });
    // Filter tabel kelas & mapel berdasarkan semester (AJAX, tanpa partial)
$('#semesterKelasFilter').on('change', function() {
    let semester = $(this).val();
    let container = $('#kelasMapelContainer');
    container.html('<div class="text-center py-5"><div class="spinner-border text-primary"></div><p>Memuat data...</p></div>');

    $.ajax({
        url: '{{ route("filterDataKelas") }}',
        data: { semester: semester },
        success: function(res) {
            if (res.length > 0) {
                let html = `
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>Kelas</th>
                                    <th>Mata Pelajaran</th>
                                    <th class="text-center">Jumlah Siswa</th>
                                    <th class="text-center">Nilai Tersimpan</th>
                                    <th class="text-center">Rata-rata</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                res.forEach((item, idx) => {
                    let rata = item.rata_rata_nilai;
                    let color = rata >= 85 ? 'success' : (rata >= 75 ? 'info' : 'warning');
                    html += `
                        <tr>
                            <td class="text-center">${idx+1}</td>
                            <td><strong>${escapeHtml(item.kelas)}</strong></td>
                            <td>${escapeHtml(item.mapel)}</td>
                            <td class="text-center"><span class="badge badge-primary">${item.jumlah_siswa}</span></td>
                            <td class="text-center"><span class="badge badge-info">${item.nilai_tersimpan}</span></td>
                            <td class="text-center"><strong class="text-${color}">${rata.toFixed(2)}</strong></td>
                        </tr>
                    `;
                });
                html += `</tbody></table></div>`;
                container.html(html);
            } else {
                container.html(`
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-gray-300"></i>
                        <p class="mt-2">Belum ada data nilai untuk semester ${semester}</p>
                        <a href="{{ route('DataNilai') }}" class="btn btn-sm btn-primary">Input Nilai</a>
                    </div>
                `);
            }
        },
        error: function() {
            container.html('<div class="text-center py-5 text-danger">Gagal memuat data. Silakan coba lagi.</div>');
        }
    });
});

// Helper untuk menghindari XSS
function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}
</script>

@endpush
