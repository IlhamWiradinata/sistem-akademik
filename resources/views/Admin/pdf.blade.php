<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Akademik - {{ $siswa->user->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }
        .header h3 {
            margin: 3px 0;
            font-size: 14px;
            font-weight: bold;
        }
        .header p {
            margin: 3px 0;
            font-size: 11px;
        }
        .section {
            margin-bottom: 15px;
        }
        .section-title {
            background-color: #f8f9fa;
            padding: 5px;
            font-weight: bold;
            border-left: 3px solid #007bff;
            margin-bottom: 8px;
        }
        .identity-table {
            width: 100%;
            border: none;
            margin-bottom: 10px;
        }
        .identity-table td {
            padding: 3px 0;
            border: none;
            font-size: 11px;
            vertical-align: top;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .table th, .table td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 11px;
        }
        .table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .signature {
            margin-top: 50px;
            text-align: right;
        }
        .signature div {
            margin-top: 5px;
        }
        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #000;
            display: inline-block;
            min-width: 200px;
            padding-top: 4px;
            font-weight: bold;
        }
        .print-info {
            text-align: center;
            font-size: 9px;
            margin-top: 20px;
            border-top: 1px dashed #aaa;
            padding-top: 10px;
            color: #555;
        }
    </style>
</head>
<body>

    <!-- Header Sekolah (Lengkap) -->
    <div class="header">
        <h2>PEMERINTAH PROVINSI JAWA BARAT</h2>
        <h2>DINAS PENDIDIKAN</h2>
        <h3>SMK NEGERI 1 CIPEUNDEUY</h3>
        <p>Jl. Cirata-Margalaksana RT 01 RW 12 Ds. Margalaksana, Kec. Cipeundeuy 40558 Kab. Bandung Barat</p>
        <p>Telp: 022-6973839 | Email: smkn_cipeundeuy@rocketmail.com</p>
        <hr style="border:1px solid #000;">
        <p style="font-size:14px; font-weight:bold; text-transform:uppercase;">LAPORAN AKADEMIK SISWA</p>
        <p>Semester {{ $semester }} - Tahun Ajaran {{ $tahunAjaran }}</p>
    </div>

    <!-- Identitas Siswa -->
    <div class="section">
        <div class="section-title">IDENTITAS SISWA</div>
        <table class="identity-table">
            <tr>
                <td width="20%">NIS</td>
                <td width="30%">: {{ $siswa->nis }}</td>
                <td width="20%">Kelas</td>
                <td width="30%">: {{ $kelasAktif->nama_kelas ?? '-' }}</td>
            </tr>
            <tr>
                <td>Nama Siswa</td>
                <td>: {{ $siswa->user->name }}</td>
                <td>Jurusan</td>
                <td>: {{ $siswa->jurusan->nama_jurusan ?? '-' }}</td>
            </tr>
            <tr>
                <td>Semester</td>
                <td>: {{ $semester }}</td>
                <td>Wali Kelas</td>
                <td>: {{ $waliKelas ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- Nilai Akademik -->
    <div class="section">
        <div class="section-title">NILAI AKADEMIK</div>
        <table class="table">
            <thead>
                <tr>
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
                @forelse ($nilai as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->mapel->nama_mapel ?? '-' }}</td>
                    <td class="text-center">{{ $item->nilai_tugas ?? '-' }}</td>
                    <td class="text-center">{{ $item->nilai_praktikum ?? '-' }}</td>
                    <td class="text-center">{{ $item->nilai_uts ?? '-' }}</td>
                    <td class="text-center">{{ $item->nilai_uas ?? '-' }}</td>
                    <td class="text-center">{{ $item->sikap ?? '-' }}</td>
                    <td class="text-center">{{ number_format($item->rata_rata ?? 0, 2) }}</td>
                    <td class="text-center">{{ $item->grade ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">Data nilai belum tersedia</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Kehadiran (hanya Sakit, Izin, Alpha) -->
    <div class="section">
        <div class="section-title">REKAP KETIDAKHADIRAN</div>
        <table class="table">
            <thead>
                <tr>
                    <th width="33%">Sakit</th>
                    <th width="33%">Izin</th>
                    <th width="33%">Alpha</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">{{ $absensi->sakit ?? 0 }} hari</td>
                    <td class="text-center">{{ $absensi->izin ?? 0 }} hari</td>
                    <td class="text-center">{{ $absensi->alpha ?? 0 }} hari</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Catatan dan Rekomendasi -->
    <div class="section">
        <div class="section-title">CATATAN DAN REKOMENDASI</div>
        <table class="identity-table">
            <tr>
                <td width="25%"><strong>Catatan Akademik:</strong></td>
                <td>{{ $laporanakademik->catatan_akademik ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Catatan Sikap:</strong></td>
                <td>{{ $laporanakademik->catatan_sikap ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Kesimpulan:</strong></td>
                <td>{{ $laporanakademik->kesimpulan ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Rekomendasi:</strong></td>
                <td>{{ $laporanakademik->rekomendasi ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- Tanda Tangan Wali Kelas (Rata Kanan) -->
    <div class="signature">
        <div>Cipeundeuy, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
        <div>Wali Kelas {{ $kelasAktif->nama_kelas ?? '' }}</div>
        <div class="signature-line">{{ $waliKelas ?? '-' }}</div>
        <div style="font-size:9px;">NIP. {{ $nip ?? '-' }}</div>
    </div>

    <!-- Footer -->
    <div class="print-info">
        Dicetak pada {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i:s') }} melalui Sistem Akademik SMKN 1 Cipeundeuy
    </div>

</body>
</html>
