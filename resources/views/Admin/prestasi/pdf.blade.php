<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Prestasi Siswa - SMK Negeri 1 Cipeundeuy</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #000;
        }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 16px; font-weight: bold; }
        .header p { margin: 3px 0; font-size: 11px; }
        .table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        .table th, .table td { border: 1px solid #000; padding: 5px; font-size: 11px; }
        .table th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .section { margin-bottom: 15px; }
        .section-title {
            background-color: #f8f9fa; padding: 5px; font-weight: bold;
            border-left: 3px solid #007bff; margin-bottom: 5px;
        }
        .identity-table { width: 100%; border: none; }
        .identity-table td { padding: 3px 0; border: none; font-size: 11px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .signature { width: 100%; margin-top: 40px; }
        .signature td { text-align: center; vertical-align: top; padding: 5px 10px; border: none; }
        .signature-line {
            margin-top: 50px; border-top: 1px solid #000;
            display: inline-block; min-width: 200px; padding-top: 4px; font-weight: bold;
        }
        .print-info {
            text-align: center; font-size: 9px; margin-top: 15px;
            border-top: 1px dashed #aaa; padding-top: 10px; color: #555;
        }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <!-- Kop Sekolah -->
    <div class="header">
        <h2>PEMERINTAH PROVINSI JAWA BARAT</h2>
        <h2>DINAS PENDIDIKAN</h2>
        <h2 style="font-size:18px; margin-top:3px;">SMK NEGERI 1 CIPEUNDEUY</h2>
        <p>Jl. Cirata-Margalaksana RT 01 RW 12 Ds. Margalaksana, Kec. Cipeundeuy 40558 Kab. Bandung Barat</p>
        <p>Telp: 022-6973839 | Email: smkn_cipeundeuy@rocketmail.com</p>
        <hr style="border:1px solid #000;">
        <p style="font-size:14px; font-weight:bold; text-transform:uppercase;">LAPORAN PRESTASI SISWA</p>
        <p>{{ $title }}</p>
        <p>{{ $subtitle }}</p>
    </div>

    <!-- Info Laporan -->
    <div class="section">
        <div class="section-title">INFORMASI LAPORAN</div>
        <table class="identity-table">
            <tr>
                <td width="25%">Tahun Ajaran</td>
                <td width="75%">: {{ $tahun_ajaran }}</td>
            </tr>
            <tr><td>Semester</td><td>: {{ $semester }}</td></tr>
            <tr>
                <td>Jenis Laporan</td>
                <td>: {{ $jenis == 'ranking_kelas' ? 'Ranking Kelas' : 'Juara Umum' }}</td>
            </tr>
            <tr><td>Total Data</td><td>: {{ count($data) }} siswa</td></tr>
            <tr><td>Tanggal Cetak</td><td>: {{ $tanggal_cetak }}</td></tr>
        </table>
    </div>

    <!-- Statistik Singkat -->
    <div class="section">
        <div class="section-title">STATISTIK</div>
        <table class="table">
            <tr>
                <th>Total Siswa</th>
                <th>Juara 1-3</th>
                <th>Rata-rata Skor Total</th>
            </tr>
            <tr>
                <td class="text-center">{{ count($data) }}</td>
                <td class="text-center">{{ $data->where('ranking', '<=', 3)->count() }}</td>
                <td class="text-center">
                    @if(count($data) > 0)
                        {{ number_format($data->avg('skor_total'), 2) }}
                    @else 0 @endif
                </td>
            </tr>
        </table>

        {{-- Tambahan: ringkasan kategori DT --}}
        @php
            $kategoriGroup = $data->groupBy('kategori_dt')->map->count();
            $urutan = [
                'Berprestasi Unggul',
                'Berprestasi Baik',
                'Berkembang Sesuai Harapan',
                'Berkembang dengan Bimbingan',
                'Memerlukan Pembinaan Khusus',
            ];
        @endphp
        <table class="table" style="margin-top:5px;">
            <thead>
                <tr>
                    <th colspan="2">Distribusi Kategori Decision Tree</th>
                </tr>
                <tr>
                    <th>Kategori</th>
                    <th>Jumlah Siswa</th>
                </tr>
            </thead>
            <tbody>
                @foreach($urutan as $kat)
                    @if(isset($kategoriGroup[$kat]))
                    <tr>
                        <td>{{ $kat }}</td>
                        <td class="text-center">{{ $kategoriGroup[$kat] }}</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Kriteria Penilaian -->
    <div class="section">
        <div class="section-title">KRITERIA PENILAIAN</div>
        <table class="identity-table">
            <tr><td width="40%">Nilai Akademik </td><td>Bobot 50%</td></tr>
            <tr><td>Persentase Kehadiran</td><td>Bobot 30%</td></tr>
            <tr><td>Perilaku</td><td>Bobot 20%</td></tr>
        </table>
    </div>

    @if($format == 'detail')
    <!-- Tabel Detail Ranking -->
    <div class="section">
        <div class="section-title">DAFTAR PERINGKAT SISWA</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>NIS</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Nilai Rata-rata</th>
                    <th>Kehadiran (%)</th>
                    <th>Sikap</th>
                    <th>Skor Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $item)
                <tr>
                    <td class="text-center">{{ $item->ranking }}</td>
                    <td class="text-center">{{ $item->siswa->nis ?? '-' }}</td>
                    <td class="text-left">{{ $item->siswa->user->name ?? '-' }}</td>
                    <td class="text-center">{{ $item->kelas->nama_kelas ?? '-' }}</td>
                    <td class="text-center">{{ number_format($item->nilai_rata_rata, 2) }}</td>
                    <td class="text-center">{{ number_format($item->persentase_kehadiran, 2) }}%</td>
                    <!-- SIKAP dihitung dari nilai_perilaku, bukan dari field sikap -->
                    <td class="text-center">
                        @php
                            $np = $item->nilai_perilaku;
                            if ($np >= 90) $sikapHuruf = 'A';
                            elseif ($np >= 75) $sikapHuruf = 'B';
                            elseif ($np >= 60) $sikapHuruf = 'C';
                            elseif ($np >= 45) $sikapHuruf = 'D';
                            else $sikapHuruf = 'E';
                        @endphp
                        {{ $sikapHuruf }}
                    </td>
                    <td class="text-center"><strong>{{ number_format($item->skor_total, 2) }}</strong></td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center"><em>Tidak ada data</em></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    <!-- Penghargaan Juara -->
    @php $topThree = $data->where('ranking', '<=', 3); @endphp
    @if($topThree->count() > 0)
    <div class="section">
        <div class="section-title">PENGHARGAAN JUARA</div>
        <p style="text-align:center; margin:10px 0;">Diberikan kepada:</p>
        <table class="table">
            <thead>
                <tr>
                    <th>Peringkat</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>NIS</th>
                    <th>Skor Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topThree as $item)
                <tr>
                    <td class="text-center">
                        @if($item->ranking == 1) Juara 1
                        @elseif($item->ranking == 2) Juara 2
                        @elseif($item->ranking == 3) Juara 3
                        @endif
                    </td>
                    <td class="text-left"><strong>{{ $item->siswa->user->name ?? '-' }}</strong></td>
                    <td class="text-center">{{ $item->kelas->nama_kelas ?? '-' }}</td>
                    <td class="text-center">{{ $item->siswa->nis ?? '-' }}</td>
                    <td class="text-center"><strong>{{ number_format($item->skor_total, 2) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p style="text-align:center; font-style:italic; margin-top:10px;">
            “Selamat kepada para juara. Jadilah teladan dan terus tingkatkan prestasi.”
        </p>
    </div>
    @endif

    <!-- Tanda Tangan -->
    <table class="signature">
        <tr>
            <td width="50%">
                Mengetahui,<br>
                Kepala SMK Negeri 1 Cipeundeuy
                <div class="signature-line">Agus Kusnadi, S.Pd., M.Pd.</div>
                <div style="font-size:9px;">NIP. -</div>
            </td>
            <td width="50%">
                Cipeundeuy, {{ date('d F Y') }}<br>
                Wakasek Kurikulum
                <div class="signature-line">Ida Ariswati, M.Pd.</div>
                <div style="font-size:9px;">NIP. 197104071994122001</div>
            </td>
        </tr>
    </table>

    <div class="print-info">
        Dicetak pada {{ $tanggal_cetak }} melalui Sistem Akademik SMKN 1 Cipeundeuy
    </div>
</body>
</html>
