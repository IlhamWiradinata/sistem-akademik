@extends('layouts.layoutsadmin.app')

@section('title')
<title>Sistem Akademik - Hasil Prestasi Siswa</title>
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-chart-bar"></i> Hasil Prestasi Siswa
    </h1>
    <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<!-- Filter Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-filter"></i> Filter Data
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('prestasi.hasil') }}">
            <div class="row">
                <div class="col-md-3">
                    <label class="font-weight-bold">Tahun Ajaran</label>
                    <select name="tahun_ajaran" class="form-control">
                        @foreach($tahunAjaranList as $ta)
                            <option value="{{ $ta }}" {{ $tahunAjaran == $ta ? 'selected' : '' }}>
                                {{ $ta }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="font-weight-bold">Semester</label>
                    <select name="semester" class="form-control">
                        <option value="Ganjil" {{ $semester == 'Ganjil' ? 'selected' : '' }}>
                            Ganjil
                        </option>
                        <option value="Genap" {{ $semester == 'Genap' ? 'selected' : '' }}>
                            Genap
                        </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="font-weight-bold">Jenis</label>
                    <select name="jenis" id="jenisPrestasi" class="form-control">
                        <option value="ranking_kelas" {{ $jenis == 'ranking_kelas' ? 'selected' : '' }}>
                            Ranking Kelas
                        </option>
                        <option value="juara_umum" {{ $jenis == 'juara_umum' ? 'selected' : '' }}>
                            Juara Umum
                        </option>
                    </select>
                </div>

                <div class="col-md-3" id="divKelas" style="{{ $jenis == 'juara_umum' ? 'display:none;' : '' }}">
                    <label class="font-weight-bold">Kelas</label>
                    <select name="kelas_id" class="form-control">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2" id="divTingkat" style="{{ $jenis == 'ranking_kelas' ? 'display:none;' : '' }}">
                    <label class="font-weight-bold">Tingkat</label>
                    <select name="tingkat" class="form-control">
                        <option value="X" {{ $tingkat == 'X' ? 'selected' : '' }}>
                            Kelas X
                        </option>
                        <option value="XI" {{ $tingkat == 'XI' ? 'selected' : '' }}>
                            Kelas XI
                        </option>
                        <option value="XII" {{ $tingkat == 'XII' ? 'selected' : '' }}>
                            Kelas XII
                        </option>
                    </select>
                </div>

                <div class="col-md-12 mt-3">
                    <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Tampilkan
                    </button>
                    <a href="{{ route('prestasi.hasil') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Info Card -->
<div class="card border-left-info shadow mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                    Tahun Ajaran
                </div>
                <div class="h6 mb-0 font-weight-bold text-gray-800">
                    {{ $tahunAjaran }}
                </div>
            </div>

            <div class="col-md-3">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                    Semester
                </div>
                <div class="h6 mb-0 font-weight-bold text-gray-800">
                    {{ $semester }}
                </div>
            </div>

            <div class="col-md-3">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                    Jenis Prestasi
                </div>
                <div class="h6 mb-0 font-weight-bold text-gray-800">
                    {{ $jenis == 'ranking_kelas' ? 'Ranking Kelas' : 'Juara Umum' }}
                    @if($jenis == 'juara_umum')
                        - Tingkat {{ $tingkat }}
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                    Total Data
                </div>
                <div class="h6 mb-0 font-weight-bold text-gray-800">
                    {{ $hasil->total() }} Data
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Results Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table"></i> Daftar Hasil
        </h6>
        <div>
            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#exportPdfModal">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
        </div>
    </div>
    <div class="card-body">
        @if($hasil->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="bg-light">
                    <tr>
                        <th width="5%">Rank</th>
                        <th width="10%">NIS</th>
                        <th width="25%">Nama Siswa</th>
                        @if($jenis == 'ranking_kelas')
                        <th width="15%">Kelas</th>
                        @endif
                        <th width="12%" class="text-center">Nilai Rata-rata</th>
                        <th width="12%" class="text-center">Kehadiran</th>
                        <th width="10%" class="text-center">Sikap</th>
                        <th width="12%" class="text-center">Skor Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hasil as $item)
                    <tr>
                        <td class="text-center">
                            @if($item->ranking == 1)
                                <span class="badge badge-warning p-2">JUARA {{ $item->ranking }}</span>
                            @elseif($item->ranking == 2)
                                <span class="badge badge-secondary p-2">JUARA {{ $item->ranking }}</span>
                            @elseif($item->ranking == 3)
                                <span class="badge badge-success p-2">JUARA {{ $item->ranking }}</span>
                            @else
                                <span class="badge badge-info p-2">RANK {{ $item->ranking }}</span>
                            @endif
                        </td>
                        <td>{{ $item->siswa->nis }}</td>
                        <td>
                            <strong>{{ $item->siswa->user->name ?? $item->siswa->nama_lengkap }}</strong>
                        </td>
                        @if($jenis == 'ranking_kelas')
                        <td>{{ $item->kelas->nama_kelas }}</td>
                        @endif
                        <td class="text-center">
                            <span class="badge {{ $item->nilai_rata_rata >= 85 ? 'badge-success' : ($item->nilai_rata_rata >= 75 ? 'badge-info' : 'badge-warning') }} p-2">
                                {{ number_format($item->nilai_rata_rata, 2) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $item->persentase_kehadiran >= 90 ? 'badge-success' : ($item->persentase_kehadiran >= 75 ? 'badge-info' : 'badge-warning') }} p-2">
                                {{ number_format($item->persentase_kehadiran, 2) }}%
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light border">
                                @php
                                    $np = $item->nilai_perilaku;
                                    if ($np >= 90) $sikapHuruf = 'A';
                                    elseif ($np >= 75) $sikapHuruf = 'B';
                                    elseif ($np >= 60) $sikapHuruf = 'C';
                                    elseif ($np >= 45) $sikapHuruf = 'D';
                                    else $sikapHuruf = 'E';
                                @endphp
                                {{ $sikapHuruf }}
                            </span>
                        </td>
                        <td class="text-center">
                            <strong class="text-primary">{{ number_format($item->skor_total, 2) }}</strong>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
            <nav>
                {{ $hasil->withQueryString()->links('pagination::bootstrap-4') }}
            </nav>
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Tidak ada data ditemukan</h5>
            <p class="text-muted">Coba ubah filter atau buat ranking terlebih dahulu.</p>
        </div>
        @endif
    </div>
</div>

{{-- Modal Export PDF --}}
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-pdf text-danger"></i> Export ke PDF
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- Ubah method menjadi GET --}}
                <form id="formExportPdf" method="GET" action="{{ route('prestasi.export-pdf') }}" target="_blank">
                    {{-- Hapus @csrf karena tidak diperlukan untuk GET --}}

                    <input type="hidden" name="format" value="detail">

                    <div class="form-group">
                        <label class="font-weight-bold">Jenis Laporan</label>
                        <select name="jenis" id="jenis_laporan" class="form-control" required>
                            <option value="">-- Pilih Jenis Laporan --</option>
                            <option value="ranking_kelas">Ranking Kelas</option>
                            <option value="juara_umum">Juara Umum</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Tahun Ajaran</label>
                        <select name="tahun_ajaran" id="tahun_ajaran_pdf" class="form-control" required>
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            @foreach($tahunAjaranList as $ta)
                                <option value="{{ $ta }}">{{ $ta }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Semester</label>
                        <select name="semester" id="semester_pdf" class="form-control" required>
                            <option value="Ganjil">Semester Ganjil</option>
                            <option value="Genap">Semester Genap</option>
                        </select>
                    </div>

                    {{-- Field untuk Ranking Kelas --}}
                    <div id="field_kelas" style="display: none;">
                        <div class="form-group">
                            <label class="font-weight-bold">Kelas</label>
                            <select name="kelas_id" id="kelas_pdf" class="form-control">
                                <option value="">-- Pilih Kelas --</option>
                            </select>
                        </div>
                    </div>

                    {{-- Field untuk Juara Umum --}}
                    <div id="field_tingkat" style="display: none;">
                        <div class="form-group">
                            <label class="font-weight-bold">Tingkat</label>
                            <select name="tingkat" id="tingkat_pdf" class="form-control">
                                <option value="X">Kelas X</option>
                                <option value="XI">Kelas XI</option>
                                <option value="XII">Kelas XII</option>
                            </select>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Anda akan diarahkan ke halaman cetak.
                        Gunakan fitur <strong>Print > Save as PDF</strong> di browser Anda.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                {{-- Ubah menjadi submit button --}}
                <button type="submit" form="formExportPdf" class="btn btn-danger">
                    <i class="fas fa-external-link-alt"></i> Buka Halaman Cetak
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle filter berdasarkan jenis prestasi
        $('#jenisPrestasi').change(function() {
            if ($(this).val() === 'ranking_kelas') {
                $('#divKelas').show();
                $('#divTingkat').hide();
            } else {
                $('#divKelas').hide();
                $('#divTingkat').show();
            }
        });

        // Export PDF
        $('#btnExportPdf').click(function() {
            const tahunAjaran = $('[name="tahun_ajaran"]').val();
            const semester = $('[name="semester"]').val();
            const jenis = $('[name="jenis"]').val();
            const kelasId = $('[name="kelas_id"]').val();
            const tingkat = $('[name="tingkat"]').val();

            Swal.fire({
                title: 'Export PDF',
                text: 'Sedang menyiapkan file...',
                allowOutsideClick: false,
                onBeforeOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route("prestasi.export-pdf") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    tahun_ajaran: tahunAjaran,
                    semester: semester,
                    jenis: jenis,
                    kelas_id: kelasId,
                    tingkat: tingkat
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(data) {
                    Swal.close();

                    const blob = new Blob([data], { type: 'application/pdf' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `prestasi-siswa-${tahunAjaran}-${semester}.pdf`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Gagal mengenerate PDF',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

            // ================ EXPORT PDF FUNCTIONALITY ================

    // Toggle field berdasarkan jenis laporan
    $('#jenis_laporan').change(function() {
        const jenis = $(this).val();

        if (jenis === 'ranking_kelas') {
            $('#field_kelas').show();
            $('#field_tingkat').hide();
            $('#kelas_pdf').prop('required', true);
            $('#tingkat_pdf').prop('required', false);
        } else if (jenis === 'juara_umum') {
            $('#field_kelas').hide();
            $('#field_tingkat').show();
            $('#kelas_pdf').prop('required', false);
            $('#tingkat_pdf').prop('required', true);
        } else {
            $('#field_kelas').hide();
            $('#field_tingkat').hide();
            $('#kelas_pdf').prop('required', false);
            $('#tingkat_pdf').prop('required', false);
        }
    });

    // Load kelas untuk export PDF
    $('#tahun_ajaran_pdf').change(function() {
        const tahunAjaran = $(this).val();
        const selectKelas = $('#kelas_pdf');

        if (!tahunAjaran) {
            selectKelas.html('<option value="">-- Pilih Tahun Ajaran terlebih dahulu --</option>');
            return;
        }

        $.ajax({
            url: '{{ route("prestasi.get-kelas") }}',
            type: 'GET',
            data: { tahun_ajaran: tahunAjaran },
            success: function(response) {
                let options = '<option value="">-- Pilih Kelas --</option>';

                response.forEach(kelas => {
                    options += `<option value="${kelas.id}">${kelas.nama_kelas}</option>`;
                });

                selectKelas.html(options);
            }
        });
    });

    // Reset form saat modal ditutup
    $('#exportPdfModal').on('hidden.bs.modal', function() {
        $('#formExportPdf')[0].reset();
        $('#field_kelas').hide();
        $('#field_tingkat').hide();
        $('#kelas_pdf').html('<option value="">-- Pilih Kelas --</option>');
        $('#jenis_laporan').prop('required', false);
    });
    });
</script>
@endpush

<style>
.pagination-sm .page-link{
    padding:.25rem .5rem;
    font-size:.875rem;
}
</style>
