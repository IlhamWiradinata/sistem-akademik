@extends('layouts.layoutsguru.app')

@section('title')
<title>Laporan Akademik Siswa</title>
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-file-alt"></i> Laporan Akademik Siswa
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

<!-- Filter Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-filter"></i> Filter Data
        </h6>
    </div>
    <div class="card-body">
        <form action="{{ route('LaporanAkademikGuru') }}" method="GET" class="row align-items-end">
            <!-- Tahun Ajaran -->
            <div class="col-md-3 mb-3">
                <label class="font-weight-bold mb-2">Tahun Ajaran</label>
                <select name="tahun_ajaran" class="form-control" id="tahun_ajaran" required>
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
                <label class="font-weight-bold mb-2">Pilih Kelas</label>
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
                <label class="font-weight-bold mb-2">Semester</label>
                <select name="semester" class="form-control" required>
                    <option value="">-- Pilih Semester --</option>
                    <option value="Ganjil" {{ $semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                    <option value="Genap" {{ $semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                </select>
            </div>

            <!-- Button -->
            <div class="col-md-3 mb-3">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
            </div>
        </form>

        @if($kelasId && $semester)
        <div class="alert alert-light border mt-3 mb-0">
            <div>
                Menampilkan data kelas: <strong>{{ $kelasList->find($kelasId)->nama_kelas ?? '-' }}</strong> |
                Semester: <strong>{{ $semester }}</strong> |
                Tahun Ajaran: <strong>{{ $tahunAjaran }}</strong>
            </div>
            <div class="mt-1">
                Mata Pelajaran yang Diajar di Kelas Ini:
                <strong>{{ $mataPelajaranGuru ?? '-' }}</strong>
            </div>
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
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Nilai Terisi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $siswaDenganNilai }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
                @if($totalSiswa > 0)
                <small class="text-muted">{{ round(($siswaDenganNilai / $totalSiswa) * 100) }}% dari total siswa</small>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Kehadiran Terisi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $siswaDenganKehadiran }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                    </div>
                </div>
                @if($totalSiswa > 0)
                <small class="text-muted">{{ round(($siswaDenganKehadiran / $totalSiswa) * 100) }}% dari total siswa</small>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Laporan Lengkap</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $laporanLengkap }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                    </div>
                </div>
                @if($totalSiswa > 0)
                <small class="text-muted">{{ round(($laporanLengkap / $totalSiswa) * 100) }}% dari total siswa</small>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Daftar Siswa -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <div>
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Laporan Akademik Siswa - {{ $kelasList->find($kelasId)->nama_kelas ?? '' }}
            </h6>
        </div>
        <div>
            <a href="{{ route('unduhGuru') }}?kelas_id={{ $kelasId }}&semester={{ $semester }}&tahun_ajaran={{ $tahunAjaran }}"
               class="btn btn-danger btn-sm" target="_blank">
                <i class="fas fa-file-pdf"></i> Unduh PDF
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr class="text-center">
                        <th width="5%">No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Jurusan</th>
                        <th width="12%">Rata-rata Nilai<br></th>
                        <th width="10%">Grade</th>
                        <th width="15%">Kehadiran</th>
                        <th width="12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswaList as $index => $siswa)
                    @php
                        $rataRata = $siswa->rata_rata_nilai ?? '-';
                        $grade = $siswa->grade ?? '-';
                        $persentaseKehadiran = $siswa->persentase_kehadiran ?? '-';
                        $totalHadir = $siswa->total_hadir ?? 0;
                        $totalPertemuan = $siswa->total_pertemuan ?? 0;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $siswa->nis }}</td>
                        <td><strong>{{ $siswa->user->name }}</strong></td>
                        <td class="text-center">{{ $siswa->jurusan->nama_jurusan ?? '-' }}</td>
                        <td class="text-center">
                            @if($rataRata != '-')
                                @php
                                    $rataClass = match($grade) {
                                        'A' => 'success',
                                        'B' => 'info',
                                        'C' => 'warning',
                                        default => 'danger',
                                    };
                                @endphp
                                <span class="h5 mb-0 font-weight-bold text-{{ $rataClass }}">{{ $rataRata }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($grade != '-')
                                @php
                                    $gradeClass = match($grade) {
                                        'A' => 'success',
                                        'B' => 'info',
                                        'C' => 'warning',
                                        default => 'danger',
                                    };
                                @endphp
                                <span class="badge badge-{{ $gradeClass }} p-2"><strong>{{ $grade }}</strong></span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($persentaseKehadiran != '-')
                                @php
                                    $kehadiranColor = $persentaseKehadiran >= 80 ? 'success' : ($persentaseKehadiran >= 60 ? 'warning' : 'danger');
                                @endphp
                                <div class="mb-1">
                                    <span class="font-weight-bold text-{{ $kehadiranColor }}">{{ $persentaseKehadiran }}%</span>
                                </div>
                                <small class="text-muted">{{ $totalHadir }}/{{ $totalPertemuan }}</small><br>
                                <small class="badge badge-{{ $kehadiranColor }}">{{ $siswa->status_kehadiran }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-info btn-sm btn-detail"
                                    data-siswa-id="{{ $siswa->id }}"
                                    data-toggle="modal" data-target="#detailModal">
                                <i class="fas fa-eye"></i> Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <p class="mb-0">Tidak ada siswa di kelas ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="card shadow mb-4">
    <div class="card-body text-center py-5">
        <i class="fas fa-filter fa-4x text-gray-300 mb-3"></i>
        <h5 class="text-gray-600">Silakan pilih Kelas dan Semester</h5>
        <p class="text-muted">Gunakan filter di atas untuk menampilkan laporan akademik siswa</p>
    </div>
</div>
@endif

<!-- Modal Detail Siswa -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-graduate"></i> Detail Nilai &amp; Kehadiran Siswa
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detailModalContent">
                <!-- Akan diisi JavaScript -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Filter Tahun Ajaran -> reload Kelas
    $('#tahun_ajaran').on('change', function() {
        const tahunAjaran = $(this).val();
        const kelasSelect = $('#kelas_id');
        if (!tahunAjaran) {
            kelasSelect.html('<option value="">-- Pilih Kelas --</option>');
            return;
        }
        kelasSelect.html('<option value="">Memuat...</option>');
        $.get('{{ route("kelas.by.tahun.ajaran") }}', { tahun_ajaran: tahunAjaran })
            .done(function(data) {
                let options = '<option value="">-- Pilih Kelas --</option>';
                data.forEach(kelas => {
                    options += `<option value="${kelas.id}">${kelas.nama_kelas}</option>`;
                });
                kelasSelect.html(options);
            })
            .fail(function() {
                kelasSelect.html('<option value="">-- Error memuat kelas --</option>');
            });
    });

    // Tombol Detail
    $(document).on('click', '.btn-detail', function() {
        const siswaId = $(this).data('siswa-id');
        const semester = '{{ $semester }}';
        const tahunAjaran = '{{ $tahunAjaran }}';
        const kelasId = '{{ $kelasId }}';

        const row = $(this).closest('tr');
        const nis = row.find('td:nth-child(2)').text().trim();
        const nama = row.find('td:nth-child(3) strong').text();
        const jurusan = row.find('td:nth-child(4)').text().trim();
        const rataRata = row.find('td:nth-child(5) .font-weight-bold').first().text().trim() || '-';
        const grade = row.find('td:nth-child(6) .badge strong').text() || '-';
        const kehadiranPersen = row.find('td:nth-child(7) .font-weight-bold').text().replace('%', '').trim() || '0';
        const kehadiranText = row.find('td:nth-child(7) small.text-muted').text();
        const [totalHadir, totalPertemuan] = kehadiranText ? kehadiranText.split('/') : ['0', '0'];
        const statusKehadiran = row.find('td:nth-child(7) .badge').text() || '-';

        showModalWithCachedData(nis, nama, jurusan, rataRata, grade, {
            total_hadir: parseInt(totalHadir) || 0,
            total_pertemuan: parseInt(totalPertemuan) || 0,
            persentase: parseFloat(kehadiranPersen) || 0,
            status: statusKehadiran
        });

        loadDetailData(siswaId, semester, tahunAjaran, kelasId, nis, nama, jurusan, rataRata, grade);
    });

    function showModalWithCachedData(nis, nama, jurusan, rataRata, grade, kehadiranData) {
        let gradeClass = 'secondary';
        if (grade === 'A') gradeClass = 'success';
        else if (grade === 'B') gradeClass = 'info';
        else if (grade === 'C') gradeClass = 'warning';
        else if (grade === 'D' || grade === 'E') gradeClass = 'danger';

        const kehadiranHtml = renderKehadiranHTML(kehadiranData, true);

        $('#detailModalContent').html(`
            <div class="vertical-layout">
                <div class="mb-4">
                    <div class="card border-primary">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3"><h6 class="text-muted">NIS</h6><p class="font-weight-bold">${nis}</p></div>
                                <div class="col-md-3"><h6 class="text-muted">Nama</h6><p class="font-weight-bold">${nama}</p></div>
                                <div class="col-md-3"><h6 class="text-muted">Jurusan</h6><p class="font-weight-bold">${jurusan}</p></div>
                                <div class="col-md-3"><h6 class="text-muted">Rata-rata</h6><p class="font-weight-bold">${rataRata} <span class="badge badge-${gradeClass}">${grade}</span></p></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="attendance-section mb-3">
                    <div class="card shadow">
                        <div class="card-header text-primary py-3">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-calendar-alt"></i> Detail Kehadiran
                                <span class="badge badge-primary float-right">${kehadiranData.total_pertemuan} Pertemuan</span>
                            </h6>
                        </div>
                        <div class="card-body">${kehadiranHtml}</div>
                    </div>
                </div>
                <div class="grades-section">
                    <div class="card shadow">
                        <div class="card-header text-primary py-3">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-graduation-cap"></i> Detail Nilai Mata Pelajaran (Diajar oleh Anda)
                                <span class="badge badge-primary float-right loading-pulse">Memuat...</span>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="mt-3">Memuat data nilai...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `);
        $('#detailModal').modal('show');
    }

    function loadDetailData(siswaId, semester, tahunAjaran, kelasId, nis, nama, jurusan, rataRata, grade) {
        const url = `/guru/laporan-akademik/detail/${siswaId}`;
        $.ajax({
            url: url,
            type: 'GET',
            data: { semester: semester, tahun_ajaran: tahunAjaran, kelas_id: kelasId },
            success: function(response) {
                if (response.nilai && response.nilai.length > 0) {
                    renderNilaiTable(response.nilai, grade);
                    $('.grades-section .card-header .badge').text(response.nilai.length + ' Mapel');
                } else {
                    $('.grades-section .card-body').html(`
                        <div class="text-center py-5">
                            <i class="fas fa-info-circle fa-4x text-muted mb-3"></i>
                            <h6 class="text-muted">Belum ada data nilai untuk mata pelajaran yang Anda ajar di kelas ini</h6>
                            <p class="text-muted">Silakan input nilai terlebih dahulu.</p>
                        </div>
                    `);
                    $('.grades-section .card-header .badge').text('0 Mapel');
                }
                $('.grades-section .card-header .badge').removeClass('loading-pulse');

                if (response.total_pertemuan !== undefined) {
                    const kehadiranData = {
                        total_hadir: response.total_hadir || 0,
                        total_pertemuan: response.total_pertemuan || 0,
                        total_izin: response.total_izin || 0,
                        total_sakit: response.total_sakit || 0,
                        total_alpha: response.total_alpha || 0,
                        persentase: response.total_pertemuan > 0 ? ((response.total_hadir || 0) / response.total_pertemuan * 100).toFixed(1) : 0
                    };
                    $('.attendance-section .card-body').html(renderKehadiranHTML(kehadiranData, false));
                    $('.attendance-section .card-header .badge').text(kehadiranData.total_pertemuan + ' Pertemuan');
                }
            },
            error: function() {
                $('.grades-section .card-body').html(`
                    <div class="alert alert-danger">
                        <h6>Gagal memuat data nilai</h6>
                        <p>Terjadi kesalahan. Silakan coba lagi.</p>
                        <button class="btn btn-sm btn-primary" onclick="location.reload()">Refresh</button>
                    </div>
                `);
                $('.grades-section .card-header .badge').text('Error').removeClass('badge-primary').addClass('badge-danger');
            }
        });
    }

    function renderNilaiTable(nilaiData, overallGrade) {
        let html = `
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th width="30%">Mata Pelajaran</th>
                            <th width="12%" class="text-center">Tugas</th>
                            <th width="12%" class="text-center">UTS</th>
                            <th width="12%" class="text-center">UAS</th>
                            <th width="12%" class="text-center">Praktikum</th>
                            <th width="12%" class="text-center">Rata-rata</th>
                            <th width="10%" class="text-center">Grade</th>
                        </tr>
                    </thead>
                    <tbody>`;
        let totalRata = 0, count = 0;
        nilaiData.forEach(nilai => {
            const mapel = nilai.mapel?.nama_mapel || '-';
            const tugas = nilai.nilai_tugas || '-';
            const uts = nilai.nilai_uts || '-';
            const uas = nilai.nilai_uas || '-';
            const praktikum = nilai.nilai_praktikum || '-';
            const rata = nilai.rata_rata || 0;
            const gradeNilai = nilai.grade || '-';
            let gradeClass = 'secondary';
            if (gradeNilai === 'A') gradeClass = 'success';
            else if (gradeNilai === 'B') gradeClass = 'info';
            else if (gradeNilai === 'C') gradeClass = 'warning';
            else if (gradeNilai === 'D') gradeClass = 'danger';

            if (rata !== '-' && !isNaN(rata)) {
                totalRata += parseFloat(rata);
                count++;
            }
            html += `<tr>
                <td class="font-weight-bold">${mapel}</td>
                <td class="text-center">${tugas}</td>
                <td class="text-center">${uts}</td>
                <td class="text-center">${uas}</td>
                <td class="text-center">${praktikum}</td>
                <td class="text-center font-weight-bold ${rata < 75 ? 'text-danger' : 'text-success'}">${rata}</td>
                <td class="text-center"><span class="badge badge-${gradeClass}">${gradeNilai}</span></td>
            </tr>`;
        });
        const avg = count > 0 ? (totalRata / count).toFixed(2) : '0.00';
        let overallClass = 'secondary';
        if (overallGrade === 'A') overallClass = 'success';
        else if (overallGrade === 'B') overallClass = 'info';
        else if (overallGrade === 'C') overallClass = 'warning';
        else if (overallGrade === 'D') overallClass = 'danger';

        html += `</tbody>
            <tfoot class="bg-light">
                <tr>
                    <td colspan="5" class="text-right font-weight-bold">Rata-rata Keseluruhan:</td>
                    <td class="text-center font-weight-bold h5 ${avg < 75 ? 'text-danger' : 'text-success'}">${avg}</td>
                    <td class="text-center"><span class="badge badge-${overallClass}">${overallGrade}</span></td>
                </tr>
            </tfoot>
        </table></div>`;
        $('.grades-section .card-body').html(html);
    }

    function renderKehadiranHTML(data, isCached = false) {
        const hadir = data.total_hadir || 0;
        const total = data.total_pertemuan || 0;
        const izin = data.total_izin || 0;
        const sakit = data.total_sakit || 0;
        const alpha = data.total_alpha || 0;
        const persen = data.persentase || (total > 0 ? (hadir / total * 100).toFixed(1) : 0);
        let status = data.status || (persen >= 80 ? 'Baik' : (persen >= 60 ? 'Cukup' : 'Kurang'));
        let color = persen >= 80 ? 'success' : (persen >= 60 ? 'warning' : 'danger');

        return `
            <div class="mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="font-weight-bold">Persentase Kehadiran</span>
                    <span class="font-weight-bold">${persen}%</span>
                </div>
                <div class="progress" style="height:20px">
                    <div class="progress-bar bg-${color}" style="width:${persen}%">${persen}%</div>
                </div>
                <div class="text-center mt-2">
                    <span class="badge badge-${color} px-3 py-1">${status} ${isCached ? '(Data Sementara)' : ''}</span>
                </div>
            </div>
            <div class="row text-center mb-4">
                <div class="col-3"><div class="card border-success p-2"><h3 class="text-success">${hadir}</h3><small>Hadir</small></div></div>
                <div class="col-3"><div class="card border-warning p-2"><h3 class="text-warning">${izin}</h3><small>Izin</small></div></div>
                <div class="col-3"><div class="card border-info p-2"><h3 class="text-info">${sakit}</h3><small>Sakit</small></div></div>
                <div class="col-3"><div class="card border-danger p-2"><h3 class="text-danger">${alpha}</h3><small>Alpha</small></div></div>
            </div>
            <div class="bg-light p-3 rounded">
                <h6><i class="fas fa-chart-pie"></i> Ringkasan</h6>
                <div class="row"><div class="col-6">Total Hadir: ${hadir} dari ${total}</div><div class="col-6 text-right">Kehadiran: ${persen}%</div></div>
                <div class="row"><div class="col-6">Tidak Hadir: ${izin + sakit + alpha}</div><div class="col-6 text-right">Status: <span class="badge badge-${color}">${status}</span></div></div>
            </div>
        `;
    }
});
</script>
<style>
    .vertical-layout { display: flex; flex-direction: column; gap: 20px; }
    #detailModal .modal-dialog { max-width: 95%; width: 95%; }
    #detailModal .modal-body { max-height: 70vh; overflow-y: auto; }
    .loading-pulse { animation: pulse 1.5s infinite; }
    @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
</style>
@endpush
