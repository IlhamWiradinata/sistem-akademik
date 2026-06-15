<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Mata Pelajaran - SMK Negeri 1 Cipeundeuy</title>
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
        .stat-container {
            margin-bottom: 20px;
        }

        .stat-box {
            background: linear-gradient(135deg, #4e73df, #224abe);
            color: #fff;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: linear-gradient(135deg, #4e73df, #224abe);
            color: #000000;
        }

        th, td {
            border: 1px solid #dee2e6;
            padding: 8px;
            font-size: 10px;
            text-align: center;
        }

        tbody tr:nth-child(even) {
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

        .badge-primary { background: #4e73df; }
        .badge-success { background: #1cc88a; }

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
        <div class="document-title">Daftar Mata Pelajaran</div>
        <div class="document-subtitle">
            Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }}
        </div>
    </div>

    <!-- INFO BOX -->
    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Tanggal Cetak</span> :
            {{ date('d F Y, H:i') }} WIB
        </div>
        <div class="info-row">
            <span class="info-label">Total Mata Pelajaran</span> :
            {{ count($mapel) }} Mapel
        </div>
        <div class="info-row">
            <span class="info-label">Status Dokumen</span> : Resmi
        </div>
    </div>

    <!-- TABLE -->
    <div class="table-title">Daftar Mata Pelajaran</div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kode</th>
                <th width="30%">Nama Mata Pelajaran</th>
                <th width="20%">Kelompok</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mapel as $index => $m)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <span class="badge badge-primary">{{ $m->kode_mapel }}</span>
                </td>
                <td class="text-left"><strong>{{ $m->nama_mapel }}</strong></td>
                <td>
                    <span class="badge badge-success">{{ $m->kelompok }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="no-data">
                    Tidak ada data mata pelajaran
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- FOOTER -->
    <div class="footer">
        <table class="signature">
            <tr>
                <td width="50%">
                    Mengetahui,<br>Kepala Sekolah
                    <div class="signature-name">_________________________</div>
                    <div style="font-size: 9px; margin-top: 5px;">NIP. ___________________</div>
                </td>
                <td width="50%">
                    Cipeundeuy, {{ date('d F Y') }}<br>Waka Kurikulum
                    <div class="signature-name">_________________________</div>
                    <div style="font-size: 9px; margin-top: 5px;">NIP. ___________________</div>
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
