<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Akademik Kelas</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 15px; color: #000; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h2 { margin: 0; font-size: 14px; font-weight: bold; }
        .header p { margin: 2px 0; font-size: 10px; }
        .section-title { background-color: #f8f9fa; padding: 4px 5px; font-weight: bold; border-left: 3px solid #007bff; margin-bottom: 5px; font-size: 10px; }
        .info-table { width: 100%; border: none; margin-bottom: 10px; }
        .info-table td { padding: 2px 0; border: none; font-size: 10px; }
        .table { width: 100%; border-collapse: collapse; margin: 8px 0; }
        .table th, .table td { border: 1px solid #000; padding: 3px 3px; font-size: 8.5px; }
        .table th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .table th.group { background-color: #e0e8f0; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .signature { width: 100%; margin-top: 30px; }
        .signature td { text-align: center; vertical-align: top; padding: 5px 10px; border: none; }
        .signature-line { margin-top: 45px; border-top: 1px solid #000; display: inline-block; min-width: 180px; padding-top: 3px; font-weight: bold; font-size: 10px; }
        .print-info { text-align: center; font-size: 8px; margin-top: 12px; border-top: 1px dashed #aaa; padding-top: 8px; color: #555; }
        .ringkasan-box { background-color: #f8f9fa; padding: 6px 8px; margin-bottom: 8px; font-size: 10px; border: 1px solid #ddd; }
        .ringkasan-box span { display: inline-block; margin-right: 18px; }
    </style>
</head>
<body>

<div class="header">
    <h2>PEMERINTAH PROVINSI JAWA BARAT</h2>
    <h2>DINAS PENDIDIKAN</h2>
    <h2 style="font-size:16px; margin-top:3px;">SMK NEGERI 1 CIPEUNDEUY</h2>
    <p>Jl. Cirata-Margalaksana RT 01 RW 12 Ds. Margalaksana, Kec. Cipeundeuy 40558 Kab. Bandung Barat</p>
    <p>Telp: 022-6973839 | Email: smkn_cipeundeuy@rocketmail.com</p>
    <hr style="border:1px solid #000;">
    <p style="font-size:13px; font-weight:bold; text-transform:uppercase;">REKAP LAPORAN AKADEMIK KELAS</p>
</div>

<div class="section-title">INFORMASI KELAS DAN GURU</div>
<table class="info-table">
    <tr>
        <td width="15%">Kelas</td>
        <td width="35%">: {{ $kelas->nama_kelas }}</td>
        <td width="15%">Jurusan</td>
        <td width="35%">: {{ $siswaList->first()?->jurusan->nama_jurusan ?? '-' }}</td>
    </tr>
    <tr>
        <td>Mata Pelajaran</td>
        <td>: {{ $mapel ?: '-' }}</td>
        <td>Semester</td>
        <td>: {{ $semester }}</td>
    </tr>
    <tr>
        <td>Tahun Ajaran</td>
        <td>: {{ $tahunAjaran }}</td>
        <td>Guru Pengampu</td>
        <td>: {{ $guru->user->name ?? 'Guru' }} ({{ $guru->nip ?? '-' }})</td>
    </tr>
</table>

<div class="section-title">STATISTIK</div>
@php
    $jumlahSiswa = $siswaList->count();
    $jumlahNilaiTerisi = $siswaList->where('rata_rata_nilai', '!=', '-')->count();
    $rataKelas = $siswaList->where('rata_rata_nilai', '!=', '-')
        ->avg(fn($s) => (float) $s->rata_rata_nilai);
@endphp
<div class="ringkasan-box">
    <span><strong>Jumlah Siswa:</strong> {{ $jumlahSiswa }}</span>
    <span><strong>Nilai Terisi:</strong> {{ $jumlahNilaiTerisi }} siswa</span>
    <span><strong>Rata-rata Kelas:</strong> {{ $rataKelas ? number_format($rataKelas, 2) : '-' }}</span>
</div>

<div class="section-title">RINCIAN NILAI DAN KEHADIRAN SISWA</div>
<table class="table">
    <thead>
        <tr>
            <th rowspan="2" width="4%">No</th>
            <th rowspan="2" width="10%">NIS</th>
            <th rowspan="2" width="18%">Nama Siswa</th>
            <th colspan="5" class="group">NILAI</th>
            <th colspan="4" class="group">KEHADIRAN</th>
        </tr>
        <tr>
            <th width="7%">Tugas</th>
            <th width="7%">Praktikum</th>
            <th width="7%">UTS</th>
            <th width="7%">UAS</th>
            <th width="8%">Rata-rata</th>
            <th width="5%">S</th>
            <th width="5%">I</th>
            <th width="5%">A</th>
            <th width="9%">% Hadir</th>
        </tr>
    </thead>
    <tbody>
        @forelse($siswaList as $index => $siswa)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="text-center">{{ $siswa->nis }}</td>
            <td>{{ $siswa->user->name ?? '-' }}</td>
            <td class="text-center">{{ $siswa->nilai_tugas_pdf ?? '-' }}</td>
            <td class="text-center">{{ $siswa->nilai_praktikum_pdf ?? '-' }}</td>
            <td class="text-center">{{ $siswa->nilai_uts_pdf ?? '-' }}</td>
            <td class="text-center">{{ $siswa->nilai_uas_pdf ?? '-' }}</td>
            <td class="text-center"><strong>{{ $siswa->rata_rata_nilai }}</strong></td>
            <td class="text-center">{{ $siswa->total_sakit_pdf ?? 0 }}</td>
            <td class="text-center">{{ $siswa->total_izin_pdf ?? 0 }}</td>
            <td class="text-center">{{ $siswa->total_alpha_pdf ?? 0 }}</td>
            <td class="text-center">
                {{ $siswa->persentase_kehadiran ?? 0 }}%
                <br><small>({{ $siswa->total_hadir_pdf ?? 0 }}/{{ $siswa->total_pertemuan_pdf ?? 0 }})</small>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="13" class="text-center">Tidak ada data siswa.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<table class="signature">
    <tr>
        <td width="50%">
            Mengetahui,<br>
            Kepala SMK Negeri 1 Cipeundeuy
            <div class="signature-line">Agus Kusnadi, S.Pd., M.Pd.</div>
            <div style="font-size:8px;">NIP. -</div>
        </td>
        <td width="50%">
            Cipeundeuy, {{ date('d F Y') }}<br>
            Guru Mata Pelajaran
            <div class="signature-line">{{ $guru->user->name ?? 'Guru' }}</div>
            <div style="font-size:8px;">NIP. {{ $guru->nip ?? '-' }}</div>
        </td>
    </tr>
</table>

<div class="print-info">
    Dicetak pada {{ now()->format('d/m/Y H:i:s') }} melalui Sistem Akademik SMKN 1 Cipeundeuy
</div>

</body>
</html>
