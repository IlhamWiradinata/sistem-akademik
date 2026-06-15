@extends('layouts.layoutsadmin.app')

@section('title')
<title>Sistem Akademik - Pemilihan Prestasi Siswa</title>
@endsection

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-trophy"></i> Pemilihan Prestasi Siswa
    </h1>
</div>

<div class="row">

    {{-- ===================== RANKING PER KELAS ===================== --}}
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list-ol"></i> Ranking Kelas
                </h6>
            </div>
            <div class="card-body">
                <form id="formRankingKelas">
                    @csrf

                    <div class="form-group">
                        <label class="font-weight-bold">Tahun Ajaran</label>
                        <select name="tahun_ajaran" id="tahun_ajaran_ranking" class="form-control" required>
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            @foreach($tahunAjaranList as $ta)
                                <option value="{{ $ta }}" {{ $loop->first ? 'selected' : '' }}>{{ $ta }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Semester</label>
                        <select name="semester" id="semester_ranking" class="form-control" required>
                            <option value="Ganjil">Semester Ganjil</option>
                            <option value="Genap">Semester Genap</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Kelas</label>
                        <select name="kelas_id" id="kelas_ranking" class="form-control" required
                            {{ $kelasList->isEmpty() ? 'disabled' : '' }}>
                            @if($kelasList->isEmpty())
                                <option value="">-- Pilih Tahun Ajaran terlebih dahulu --</option>
                            @else
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                                @endforeach
                            @endif
                        </select>
                        <small class="text-muted" id="kelas_ranking_info">
                            @if($kelasList->isNotEmpty())
                                Ditemukan {{ $kelasList->count() }} kelas (tahun ajaran terbaru).
                            @endif
                        </small>
                    </div>

                    <div class="alert alert-info py-2">
                        <i class="fas fa-info-circle"></i>
                        Semua siswa dalam kelas akan mendapatkan ranking secara otomatis.
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" id="btnRanking">
                        <i class="fas fa-calculator"></i> Proses Ranking Kelas
                    </button>
                </form>

                <div id="hasilRanking" style="display:none;" class="mt-3">
                    <div class="alert alert-success mb-0">
                        <h6 class="font-weight-bold mb-1">
                            <i class="fas fa-check-circle"></i> Ranking Berhasil Dibuat!
                        </h6>
                        <div id="rankingMessage"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== JUARA UMUM ===================== --}}
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-crown"></i> Juara Umum
                </h6>
            </div>
            <div class="card-body">
                <form id="formJuaraUmum">
                    @csrf

                    <div class="form-group">
                        <label class="font-weight-bold">Tahun Ajaran</label>
                        <select name="tahun_ajaran" id="tahun_ajaran_juara" class="form-control" required>
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            @foreach($tahunAjaranList as $ta)
                                <option value="{{ $ta }}" {{ $loop->first ? 'selected' : '' }}>{{ $ta }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Semester</label>
                        <select name="semester" id="semester_juara" class="form-control" required>
                            <option value="Ganjil">Semester Ganjil</option>
                            <option value="Genap">Semester Genap</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Tingkat</label>
                        <select name="tingkat" id="tingkat_juara" class="form-control" required>
                            <option value="">-- Pilih Tingkat --</option>
                            <option value="X">Kelas X</option>
                            <option value="XI">Kelas XI</option>
                            <option value="XII">Kelas XII</option>
                        </select>
                    </div>

                    {{-- Indikator kelas yang terdeteksi --}}
                    <div id="kelasTingkatInfo" style="display:none;" class="mb-3">
                        <div class="alert alert-secondary py-2 mb-0">
                            <i class="fas fa-search"></i>
                            <span id="kelasTingkatText">Mendeteksi kelas...</span>
                        </div>
                    </div>

                    <div class="alert alert-warning py-2">
                        <i class="fas fa-crown"></i>
                        10 peringkat teratas akan dipilih dari seluruh kelas pada tingkat tersebut.
                        Pastikan <strong>seluruh kelas</strong> telah diproses ranking-nya terlebih dahulu.
                    </div>

                    <div id="rankingStatusInfo" class="alert alert-secondary py-2" style="display:none;">
                        <i class="fas fa-info-circle"></i>
                        <span id="rankingStatusText"></span>
                    </div>

                    <button type="submit" class="btn btn-warning btn-block" id="btnJuara">
                        <i class="fas fa-crown"></i> Proses Juara Umum
                    </button>
                </form>

                <div id="hasilJuara" style="display:none;" class="mt-3">
                    <div class="alert alert-warning mb-0">
                        <h6 class="font-weight-bold mb-1">
                            <i class="fas fa-check-circle"></i> Juara Umum Berhasil Ditetapkan
                        </h6>
                        <div id="juaraMessage"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Links --}}
<div class="row mt-2">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-body text-center py-3">
                <a href="{{ route('prestasi.hasil') }}" class="btn btn-success">
                    <i class="fas fa-chart-bar"></i> Lihat Hasil
                </a>
                <button type="button" class="btn btn-danger ml-3"
                        data-toggle="modal" data-target="#exportPdfModal">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ===================== PROGRESS MODAL ===================== --}}
<div class="modal fade" id="progressModal" tabindex="-1" role="dialog"
     data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title mb-0" id="progressTitle">Memproses...</h6>
            </div>
            <div class="modal-body text-center py-3">
                <div class="spinner-border text-primary mb-2" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p id="progressText" class="mb-2 small">Sedang memproses data...</p>
                <div class="progress" style="height:10px;">
                    <div id="progressBar"
                         class="progress-bar progress-bar-striped progress-bar-animated"
                         role="progressbar" style="width:0%"></div>
                </div>
            </div>
        </div>
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
$(function () {

    // ============================================================
    //  HELPER: set progress bar
    // ============================================================
    function setProgress(pct, text) {
        $('#progressBar').css('width', pct + '%').attr('aria-valuenow', pct);
        if (text) $('#progressText').text(text);
    }

    function showProgress(title, text) {
        $('#progressTitle').text(title);
        setProgress(20, text);
        $('#progressModal').modal('show');
    }

    function hideProgress(callback) {
        setProgress(100, 'Selesai.');
        setTimeout(function () {
            $('#progressModal').modal('hide');
            setTimeout(function () {
                setProgress(0, '');
                if (callback) callback();
            }, 300);
        }, 600);
    }

    // ============================================================
    //  LOAD KELAS berdasarkan tahun ajaran (reusable)
    // ============================================================
    function loadKelas(tahunAjaran, $target, onDone) {
        if (!tahunAjaran) {
            $target.prop('disabled', true)
                   .html('<option value="">-- Pilih Tahun Ajaran terlebih dahulu --</option>');
            return;
        }

        $target.prop('disabled', true).html('<option value="">Memuat kelas...</option>');

        $.ajax({
            url: '{{ route("prestasi.get-kelas") }}',
            type: 'GET',
            data: { tahun_ajaran: tahunAjaran },
            success: function (response) {
                if (!response || response.length === 0) {
                    $target.html('<option value="">Tidak ada kelas ditemukan</option>');
                    return;
                }
                let opts = '<option value="">-- Pilih Kelas --</option>';
                response.forEach(function (k) {
                    opts += '<option value="' + k.id + '">' + k.nama_kelas + '</option>';
                });
                $target.html(opts).prop('disabled', false);
                if (onDone) onDone(response);
            },
            error: function () {
                $target.html('<option value="">Gagal memuat kelas</option>');
            }
        });
    }

    // ============================================================
    //  INIT: sinkronkan dropdown kelas ranking dengan tahun ajaran default
    //  Jika tahun ajaran sudah ter-pilih dari server (blade), refresh kelas via AJAX
    //  supaya dropdown tidak disabled dan data terbaru termuat
    // ============================================================
    (function initRankingKelas() {
        const taDef = $('#tahun_ajaran_ranking').val();
        if (taDef) {
            loadKelas(taDef, $('#kelas_ranking'), function (list) {
                $('#kelas_ranking_info').text('Ditemukan ' + list.length + ' kelas (tahun ajaran terbaru).');
            });
        }
    })();

    // ============================================================
    //  RANKING KELAS — load kelas saat tahun ajaran berubah
    // ============================================================
    $('#tahun_ajaran_ranking').on('change', function () {
        $('#hasilRanking').fadeOut();
        loadKelas($(this).val(), $('#kelas_ranking'), function (list) {
            $('#kelas_ranking_info').text('Ditemukan ' + list.length + ' kelas.');
        });
    });

    // ============================================================
    //  RANKING KELAS — submit
    // ============================================================
    $('#formRankingKelas').on('submit', function (e) {
    e.preventDefault();

    if (!$('#kelas_ranking').val()) {
        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Pilih kelas terlebih dahulu.' });
        return;
    }

    showProgress('Memproses Ranking Kelas', 'Menghitung skor...');

    $.ajax({
        url: '{{ route("prestasi.proses-ranking") }}',
        type: 'POST',
        data: $(this).serialize(),
        success: function (res) {
            hideProgress(function () {
                let katCount = {};
                (res.data || []).forEach(function (s) {
                    const kat = s.kategori_dt || 'Tidak Diketahui';
                    katCount[kat] = (katCount[kat] || 0) + 1;
                });

                let katHtml = '';
                const warna = {
                     'Berprestasi Unggul'          : 'badge-primary',
                     'Berprestasi Baik'            : 'badge-success',
                     'Berkembang Sesuai Harapan'   : 'badge-info',
                     'Berkembang dengan Bimbingan' : 'badge-warning',
                     'Memerlukan Pembinaan Khusus' : 'badge-danger'
                };
                $.each(katCount, function (k, v) {
                    katHtml += '<span class="badge ' + (warna[k] || 'badge-secondary') + ' mr-1">'
                             + k + ': ' + v + '</span>';
                });

                $('#rankingMessage').html(
                    '<strong>Kelas:</strong> ' + (res.kelas || $('#kelas_ranking option:selected').text()) + '<br>' +
                    '<strong>Total Siswa:</strong> ' + (res.total_siswa ?? (res.data ? res.data.length : 0)) + ' siswa<br>' +
                    '<strong>Kategori DT:</strong> ' + (katHtml || '-') + '<br>' +
                    '<strong>Tanggal:</strong> ' + new Date().toLocaleDateString('id-ID')
                );
                $('#hasilRanking').fadeIn();

                // Setelah ranking kelas berhasil, refresh status juara umum
                checkJuaraUmumReadiness();

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message || 'Ranking kelas berhasil diproses.',
                    timer: 3000,
                    showConfirmButton: false
                });
            });
        },
        error: function (xhr) {
            hideProgress(function () {
                const msg = xhr.responseJSON
                    ? (xhr.responseJSON.message || JSON.stringify(xhr.responseJSON))
                    : 'Terjadi kesalahan server.';
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: msg });
                });
            }
        });
    });

    // ============================================================
    //  JUARA UMUM — deteksi kelas saat tahun ajaran / tingkat berubah
    // ============================================================
    function updateKelasTingkatInfo() {
        const ta      = $('#tahun_ajaran_juara').val();
        const tingkat = $('#tingkat_juara').val();

        if (!ta || !tingkat) {
            $('#kelasTingkatInfo').fadeOut();
            return;
        }

        $('#kelasTingkatInfo').fadeIn();
        $('#kelasTingkatText').text('Mendeteksi kelas tingkat ' + tingkat + '...');

        $.ajax({
            url: '{{ route("prestasi.get-kelas") }}',
            type: 'GET',
            data: { tahun_ajaran: ta },
            success: function (response) {
                // Filter kelas sesuai tingkat di client (sebagai preview)
                const filtered = response.filter(function (k) {
                    const nama = k.nama_kelas.toUpperCase();
                    if (tingkat === 'X') {
                        return nama.startsWith('X ') || nama.startsWith('X-') || nama === 'X';
                    } else if (tingkat === 'XI') {
                        return nama.startsWith('XI ') || nama.startsWith('XI-');
                    } else if (tingkat === 'XII') {
                        return nama.startsWith('XII ') || nama.startsWith('XII-');
                    }
                    // Jika ada kolom tingkat di response
                    return k.tingkat === tingkat;
                });

                if (filtered.length === 0) {
                    // Fallback: tampilkan semua sebagai info
                    const allNames = response.map(function(k){ return k.nama_kelas; }).join(', ');
                    $('#kelasTingkatText').html(
                        '<span class="text-warning"><i class="fas fa-exclamation-triangle"></i> ' +
                        'Tidak terdeteksi otomatis. Semua kelas: ' + (allNames || '-') + '</span>'
                    );
                } else {
                    const names = filtered.map(function(k){ return k.nama_kelas; }).join(', ');
                    $('#kelasTingkatText').html(
                        '<i class="fas fa-check-circle text-success"></i> ' +
                        'Terdeteksi <strong>' + filtered.length + ' kelas</strong>: ' + names
                    );
                }
            },
            error: function () {
                $('#kelasTingkatText').text('Gagal mendeteksi kelas.');
            }
        });
    }

    function checkJuaraUmumReadiness() {
    const ta      = $('#tahun_ajaran_juara').val();
    const tingkat = $('#tingkat_juara').val();
    const semester = $('#semester_juara').val();

        if (!ta || !tingkat) {
            $('#rankingStatusInfo').hide();
            $('#btnJuara').prop('disabled', true);
            return;
        }

        $.ajax({
            url: '{{ route("prestasi.check-juara-readiness") }}',
            type: 'GET',
            data: {
                tahun_ajaran: ta,
                semester: semester,
                tingkat: tingkat
            },
            success: function (res) {
                if (res.semua_sudah_ranking) {
                    $('#rankingStatusInfo').show().removeClass('alert-secondary').addClass('alert-success');
                    $('#rankingStatusText').html(
                        '<i class="fas fa-check-circle"></i> Semua kelas tingkat ' + tingkat +
                        ' sudah diproses rankingnya (' + res.total_kelas + ' kelas). Siap memilih juara umum.'
                    );
                } else {
                    $('#rankingStatusInfo').show().removeClass('alert-success').addClass('alert-warning');
                    $('#rankingStatusText').html(
                        '<i class="fas fa-exclamation-triangle"></i> Kelas yang belum ranking: ' +
                        res.belum_ranking.join(', ') + '. Anda tetap dapat melanjutkan, namun proses akan gagal jika belum lengkap.'
                    );
                }
            },
            error: function () {
                $('#rankingStatusInfo').hide();
                $('#btnJuara').prop('disabled', true);
            }
        });
    }

    $('#tahun_ajaran_juara').on('change', function () {
        $('#hasilJuara').fadeOut();
        updateKelasTingkatInfo();
    });

    $('#tingkat_juara').on('change', function () {
        $('#hasilJuara').fadeOut();
        updateKelasTingkatInfo();
    });

    // ============================================================
    //  JUARA UMUM — submit
    // ============================================================
    $('#formJuaraUmum').on('submit', function (e) {
        e.preventDefault();

        const tingkat = $('#tingkat_juara').val();
        if (!tingkat) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Pilih tingkat terlebih dahulu.' });
            return;
        }

        showProgress('Memproses Juara Umum', 'Mengumpulkan ranking semua kelas tingkat ' + tingkat + '...');

        $.ajax({
            url: '{{ route("prestasi.proses-juara") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function (res) {
                hideProgress(function () {
                    // Bangun tabel 10 besar mini
                    let rowsHtml = '';
                    (res.data || []).forEach(function (s, idx) {
                        const peringkat = idx + 1;
                        let badgeClass = 'badge-secondary';
                        let label = 'Juara ' + peringkat;
                        if (peringkat === 1) badgeClass = 'badge-warning';
                        else if (peringkat === 2) badgeClass = 'badge-secondary';
                        else if (peringkat === 3) badgeClass = 'badge-success';
                        else { badgeClass = 'badge-info'; label = 'Peringkat ' + peringkat; }

                        rowsHtml +=
                            '<tr>' +
                            '<td>' + (idx + 1) + '</td>' +
                            '<td>' + (s.nama || '-') + '</td>' +
                            '<td>' + (s.kelas || '-') + '</td>' +
                            '<td><span class="badge ' + badgeClass + '">' + label + '</span></td>' +
                            '<td><strong>' + (s.skor_total || 0) + '</strong></td>' +
                            '</tr>';
                    });

                    $('#juaraMessage').html(
                        '<strong>Tingkat:</strong> Kelas ' + res.tingkat + '<br>' +
                        '<strong>Total Juara:</strong> ' + (res.total_juara || 0) + ' siswa<br>' +
                        '<strong>Tanggal:</strong> ' + new Date().toLocaleDateString('id-ID') +
                        (rowsHtml
                            ? '<div class="table-responsive mt-2"><table class="table table-sm table-bordered mb-0" style="font-size:12px;">' +
                            '<thead><tr><th>No</th><th>Nama</th><th>Kelas</th><th>Peringkat</th><th>Skor</th></tr></thead>' +
                            '<tbody>' + rowsHtml + '</tbody></table></div>'
                            : '')
                    );
                    $('#hasilJuara').fadeIn();

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 4000,
                        showConfirmButton: false
                    });
                });
            },
            error: function (xhr) {
                hideProgress(function () {
                    let msg = 'Terjadi kesalahan server.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }

                    // Jika error karena ada kelas belum diranking, tampilkan info lebih jelas
                    const icon = xhr.status === 422 ? 'warning' : 'error';
                    const title = xhr.status === 422 ? 'Peringatan!' : 'Gagal!';

                    Swal.fire({ icon: icon, title: title, text: msg, confirmButtonText: 'OK' });
                });
            }
        });
    });

    // ============================================================
    //  EXPORT PDF — toggle field berdasarkan jenis
    // ============================================================
    $('#jenis_laporan').on('change', function () {
        const jenis = $(this).val();
        if (jenis === 'ranking_kelas') {
            $('#field_kelas').show();
            $('#field_tingkat').hide();
            $('#kelas_pdf').prop('required', true);
        } else if (jenis === 'juara_umum') {
            $('#field_kelas').hide();
            $('#field_tingkat').show();
            $('#kelas_pdf').prop('required', false);
        } else {
            $('#field_kelas, #field_tingkat').hide();
            $('#kelas_pdf').prop('required', false);
        }
    });

    $('#tahun_ajaran_pdf').on('change', function () {
        loadKelas($(this).val(), $('#kelas_pdf'));
    });

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
