<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Jadwal Kelas - SMK Negeri 1 Cipeundeuy</title>
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

        .badge-primary { background: #4e73df; }
        .badge-success { background: #1cc88a; }
        .badge-info { background: #36b9cc; }
        .badge-warning { background: #f6c23e; color: #333; }
        .badge-danger { background: #e74a3b; }
        .badge-secondary { background: #6c757d; }

        /* DAY BADGE COLORS */
        .day-senin { background: #4e73df; }
        .day-selasa { background: #1cc88a; }
        .day-rabu { background: #36b9cc; }
        .day-kamis { background: #f6c23e; color: #333; }
        .day-jumat { background: #e74a3b; }
        .day-sabtu { background: #6f42c1; }

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

        /* SUMMARY BOX */
        .summary-box {
            background: #e7f3ff;
            border: 1px solid #4e73df;
            border-radius: 5px;
            padding: 12px;
            margin-bottom: 20px;
        }

        .summary-title {
            font-weight: bold;
            color: #224abe;
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
            color: #224abe;
        }

        /* TEXT DANGER */
        .text-danger {
            color: #dc3545;
            font-weight: bold;
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
        <div class="document-title">Jadwal Pelajaran</div>
        <div class="document-subtitle">
            Tahun Ajaran {{ $tahunAjaran ?? date('Y') . '/' . (date('Y') + 1) }}
        </div>
    </div>

    <!-- INFO BOX -->
    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Tanggal Cetak</span> :
            {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y, HH:mm') }} WIB
        </div>
        <div class="info-row">
            <span class="info-label">Total Jadwal</span> :
            {{ count($jadwal) }} Sesi
        </div>
        <div class="info-row">
            <span class="info-label">Status Dokumen</span> : Resmi
        </div>
    </div>

    <!-- TABLE TITLE -->
    <div class="table-title">Daftar Jadwal Pelajaran</div>

    <!-- TABLE DATA -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Kelas</th>
                <th style="width: 12%;">Hari</th>
                <th style="width: 15%;">Jam</th>
                <th style="width: 25%;">Guru</th>
                <th style="width: 28%;">Mata Pelajaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jadwal as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $item->kelas->nama_kelas ?? '-' }}</strong></td>
                <td>
                    @php
                        $dayClass = 'day-' . strtolower($item->hari ?? '');
                    @endphp
                    <span class="badge {{ $dayClass }}">{{ $item->hari ?? '-' }}</span>
                </td>
                <td>
                    @if($item->jam_mulai && $item->jam_selesai)
                        <span class="badge badge-info">{{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }}</span>
                    @else
                        <span class="badge badge-secondary">-</span>
                    @endif
                </td>
                <td class="text-left">
                    @if($item->guru && $item->guru->user)
                        {{ $item->guru->user->name }}
                    @else
                        <span class="text-danger">Belum Ada Guru</span>
                    @endif
                </td>
                <td class="text-left">{{ $item->mapel->nama_mapel ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="no-data">
                    Tidak ada data jadwal yang tersedia
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
                    Cipeundeuy, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}<br>Wakil Kepala Sekolah Kurikulum
                    <div class="signature-name">_________________________</div>
                    <div style="font-size: 9px; margin-top: 5px;">NIP. ___________________</div>
                </td>
            </tr>
        </table>

        <div class="print-info">
            Dokumen dicetak otomatis dari Sistem Akademik SMK Negeri 1 Cipeundeuy<br>
            {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y HH:mm:ss') }} WIB
        </div>
    </div>

</body>
</html>
