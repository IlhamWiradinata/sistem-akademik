<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Kelas - SMK Negeri 1 Cipeundeuy</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            padding: 20px;
        }

        /* HEADER */
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #2c3e50;
        }

        .school-name {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
        }

        .school-address {
            font-size: 10px;
            color: #555;
        }

        .school-vision {
            font-size: 9px;
            font-style: italic;
            color: #666;
            margin-top: 5px;
        }

        .school-contact {
            font-size: 9px;
            color: #777;
            margin-top: 3px;
        }

        .document-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 15px;
            text-transform: uppercase;
        }

        .document-subtitle {
            font-size: 12px;
            color: #666;
        }

        /* INFO BOX */
        .info-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 12px;
            margin-bottom: 20px;
        }

        .info-row { margin-bottom: 6px; }
        .info-label { font-weight: bold; width: 160px; display: inline-block; }

        /* STAT BOX */
        .stats-container {
            width: 100%;
            margin-bottom: 20px;
        }

        .stats-table {
            width: 100%;
            border-collapse: collapse;
        }

        .stat-box {
            background: linear-gradient(135deg, #4e73df, #224abe);
            color: #fff;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }

        .stat-box.green {
            background: linear-gradient(135deg, #1cc88a, #13855c);
        }

        .stat-box.orange {
            background: linear-gradient(135deg, #f6c23e, #dda20a);
        }

        .stat-number {
            font-size: 26px;
            font-weight: bold;
        }

        .stat-label {
            font-size: 10px;
            text-transform: uppercase;
        }

        /* TABLE */
        .table-title {
            font-size: 14px;
            font-weight: bold;
            background: #e9ecef;
            padding: 10px;
            border-left: 4px solid #4e73df;
            margin-bottom: 10px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.data-table thead {
            background: linear-gradient(135deg, #4e73df, #224abe);
            color: #000000;
        }

        table.data-table th {
            border: 1px solid #dee2e6;
            padding: 8px;
            font-size: 10px;
            text-align: center;
        }

        table.data-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            font-size: 10px;
            text-align: center;
        }

        table.data-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .text-left { text-align: left; }

        /* BADGE */
        .badge {
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
            color: #fff;
        }

        .badge-success { background: #1cc88a; }
        .badge-warning { background: #f6c23e; color: #333; }

        /* SUMMARY BOX */
        .summary-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 5px;
            padding: 12px;
            margin-bottom: 20px;
        }

        .summary-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 8px;
        }

        .summary-table {
            width: 100%;
            border: none;
        }

        .summary-table td {
            border: none;
            padding: 5px;
            font-size: 10px;
            color: #856404;
        }

        /* FOOTER */
        .footer {
            margin-top: 35px;
            padding-top: 15px;
            border-top: 2px solid #dee2e6;
        }

        .signature {
            width: 100%;
            margin-top: 30px;
        }

        .signature td {
            border: none;
            text-align: center;
        }

        .signature-name {
            margin-top: 60px;
            font-weight: bold;
            border-top: 1px solid #333;
            display: inline-block;
            min-width: 220px;
            padding-top: 5px;
        }

        .print-info {
            text-align: center;
            font-size: 9px;
            color: #999;
            margin-top: 15px;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            font-style: italic;
            color: #999;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <div class="school-name">SMK NEGERI 1 CIPEUNDEUY</div>
        <div class="school-address">
            Jl. Cirata-Margalaksana RT 01 RW 12 Ds. Margalaksana<br>
            Kec. Cipeundeuy 40558 Kab. Bandung Barat
        </div>
        <div class="school-contact">
            Telp: 022/6973839 | Email: smkn_cipeundeuy@rocketmail.com
        </div>
        <div class="document-title">Data Kelas</div>
    </div>

    <!-- INFO BOX -->
    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Tanggal Cetak</span> :
            {{ date('d F Y, H:i') }} WIB
        </div>
        <div class="info-row">
            <span class="info-label">Total Data Kelas</span> :
            {{ count($kelas) }} Kelas
        </div>
        <div class="info-row">
            <span class="info-label">Status Dokumen</span> : Resmi
        </div>
    </div>

    <!-- TABLE TITLE -->
    <div class="table-title">Daftar Kelas</div>

    <!-- TABLE DATA -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">Nama Kelas</th>
                <th style="width: 25%;">Jurusan</th>
                <th style="width: 30%;">Wali Kelas</th>
                <th style="width: 20%;">Tahun Ajaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kelas as $index => $k)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left"><strong>{{ $k->nama_kelas }}</strong></td>
                <td class="text-left">{{ $k->jurusan->nama_jurusan ?? '-' }}</td>
                <td class="text-left">{{ $k->waliKelas->user->name ?? '-' }}</td>
                <td>{{ $k->tahun_ajaran }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="no-data">
                    Tidak ada data kelas yang tersedia
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- DETAIL PER TINGKAT -->
    @php
        $tingkatStats = [
            'X' => 0,
            'XI' => 0,
            'XII' => 0,
        ];

        foreach($kelas as $k) {
            if (strpos($k->nama_kelas, 'X') === 0 && strpos($k->nama_kelas, 'XI') === false && strpos($k->nama_kelas, 'XII') === false) {
                $tingkatStats['X']++;
            } elseif (strpos($k->nama_kelas, 'XI') === 0 && strpos($k->nama_kelas, 'XII') === false) {
                $tingkatStats['XI']++;
            } elseif (strpos($k->nama_kelas, 'XII') === 0) {
                $tingkatStats['XII']++;
            }
        }

        $totalKelasCount = count($kelas);
    @endphp

    @if(count($kelas) > 0)
    <div class="table-title" style="margin-top: 30px;">Distribusi Kelas Per Tingkat</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40%;">Tingkat</th>
                <th style="width: 30%;">Jumlah Kelas</th>
                <th style="width: 30%;">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach(['X', 'XI', 'XII'] as $tingkat)
            <tr>
                <td><strong>Kelas {{ $tingkat }}</strong></td>
                <td>{{ $tingkatStats[$tingkat] }} Kelas</td>
                <td>
                    @php
                        $persentase = $totalKelasCount > 0 ? round(($tingkatStats[$tingkat] / $totalKelasCount) * 100, 1) : 0;
                    @endphp
                    <span class="badge {{ $persentase >= 40 ? 'badge-success' : 'badge-warning' }}">
                        {{ $persentase }}%
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- FOOTER -->
    <div class="footer">
        <table class="signature">
            <tr>
                <td width="50%">
                    Mengetahui,<br>Kepala Sekolah
                    <div class="signature-name">Agus Kusnadi, S.Pd., M.Pd.</div>
                    <div style="font-size: 9px; margin-top: 5px;">NIP. ___________________</div>
                </td>
                <td width="50%">
                    Cipeundeuy, {{ date('d F Y') }}<br>Waka Kurikulum
                    <div class="signature-name">Ida Ariswati, M.Pd.</div>
                    <div style="font-size: 9px; margin-top: 5px;">NIP. 197104071994122001</div>
                </td>
            </tr>
        </table>

        <div class="print-info">
            Dokumen dicetak otomatis dari Sistem Akademik SMK Negeri 1 Cipeundeuy<br>
            {{ date('d F Y H:i:s') }} WIB
        </div>
    </div>

</body>
</html>
