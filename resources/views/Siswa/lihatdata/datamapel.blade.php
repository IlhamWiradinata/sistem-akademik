    @extends('layouts.layoutssiswa.app')

    @section('title')
    <title> Sistem Akademik - Jadwal Mata Pelajaran </title>
    @endsection

    @section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-calendar-alt"></i> Jadwal Mata Pelajaran
        </h1>
        <span class="d-none d-sm-inline-block badge badge-primary badge-lg">
            <i class="fas fa-book"></i> {{ $kelasDefault->nama_kelas ?? '-' }} / {{ $kelasDefault->jurusan->nama_jurusan ?? '-' }}
        </span>
    </div>

    <!-- Profile Card -->
    <div class="card border-left-primary shadow mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Nama Siswa</div>
                    <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $siswa->user->name }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">NIS</div>
                    <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $siswa->nis }}</div>
                </div>
            <div class="col-md-4">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Kelas & Jurusan</div>
                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                        {{ $kelasTerakhir->nama_kelas ?? '-' }}
                        @if(isset($kelasTerakhir->nama_jurusan) && $kelasTerakhir->nama_jurusan)
                            / {{ $kelasTerakhir->nama_jurusan }}
                        @elseif($siswa->jurusan)
                            / {{ $siswa->jurusan->nama_jurusan ?? '-' }}
                        @else
                            / -
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> Filter Jadwal
            </h6>
        </div>

        <div class="card-body">
            <form action="" method="GET" class="row">
                <div class="col-md-6">
                    <label class="font-weight-bold small mb-2">Pilih Kelas</label>
                    <select name="kelas" class="form-control" id="kelasSelect">
                        @foreach ($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ $kelasFilter == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }} - {{ $kelas->jurusan->nama_jurusan ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="font-weight-bold small mb-2">Pilih Hari</label>
                    <select name="hari" class="form-control">
                        <option value="">-- Semua Hari --</option>
                        <option value="Senin" {{ $hariFilter == 'Senin' ? 'selected' : '' }}>Senin</option>
                        <option value="Selasa" {{ $hariFilter == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                        <option value="Rabu" {{ $hariFilter == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                        <option value="Kamis" {{ $hariFilter == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                        <option value="Jumat" {{ $hariFilter == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                        <option value="Sabtu" {{ $hariFilter == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                    </select>
                </div>

                <div class="col-md-12 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('JadwalPelajaran') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Jadwal Section -->
    <div class="row">
        <!-- Tabel Jadwal -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list"></i> Daftar Jadwal Pelajaran
                    </h6>
                    <span class="badge badge-primary">{{ $jadwal->count() }} Jadwal</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" width="12%">Hari</th>
                                    <th class="text-center" width="15%">Jam Mulai</th>
                                    <th class="text-center" width="15%">Jam Selesai</th>
                                    <th width="30%">Mata Pelajaran</th>
                                    <th width="28%">Guru Pengampu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($jadwal as $j)
                                <tr>
                                    <td class="text-center align-middle">
                                        <span class="badge badge-info px-3 py-2">
                                            {{ $j->hari }}
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <strong>{{ \Carbon\Carbon::createFromFormat('H:i:s', $j->jam_mulai)->format('H:i') }}</strong>
                                    </td>
                                    <td class="text-center align-middle">
                                        <strong>{{ \Carbon\Carbon::createFromFormat('H:i:s', $j->jam_selesai)->format('H:i') }}</strong>
                                    </td>
                                    <td class="align-middle">
                                        <strong>{{ $j->mapel->nama_mapel ?? '-' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $j->mapel->kode_mapel ?? '-' }}</small>
                                    </td>
                                    <td class="align-middle">
                                        <small>{{ $j->guru->user->name ?? '-' }}</small>
                                        <br>
                                        <small class="text-muted">{{ $j->guru->nip ?? '-' }}</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                                        <p class="text-muted mb-0">Tidak ada jadwal pelajaran untuk filter yang dipilih</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik & Info Kelas -->
        <div class="col-lg-4 mb-4">
            <!-- Info Kelas -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> Informasi Kelas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-xs font-weight-bold text-primary text-uppercase mb-2 d-block">Nama Kelas</label>
                        <p class="h6 font-weight-bold text-gray-800">
                            {{ $kelasDefault->nama_kelas ?? 'Belum ditentukan' }}
                        </p>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="text-xs font-weight-bold text-primary text-uppercase mb-2 d-block">Jurusan</label>
                        <p class="h6 font-weight-bold text-gray-800">
                            {{ optional($kelasDefault->jurusan)->nama_jurusan ?? 'Belum ditentukan' }}
                        </p>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="text-xs font-weight-bold text-primary text-uppercase mb-2 d-block">Tingkat Kelas</label>
                        <p class="h6 font-weight-bold text-gray-800">
                            @if(!empty($kelasDefault->tingkat_kelas) && $kelasDefault->tingkat_kelas != '-')
                                {{ $kelasDefault->tingkat_kelas }}
                            @else
                                <!-- Fallback: Ekstrak dari nama_kelas jika tingkat_kelas kosong -->
                                @php
                                    $tingkat = 'Belum ditentukan';
                                    if (!empty($kelasDefault->nama_kelas)) {
                                        // Contoh: "X IPA 1" -> ambil "X"
                                        // Contoh: "XI IPS 2" -> ambil "XI"
                                        $parts = explode(' ', $kelasDefault->nama_kelas);
                                        if (count($parts) > 0) {
                                            $firstPart = $parts[0];
                                            // Validasi apakah bagian pertama adalah tingkat (X, XI, XII, dll)
                                            if (in_array($firstPart, ['X', 'XI', 'XII', '1', '2', '3', '10', '11', '12'])) {
                                                $tingkat = $firstPart;
                                            } else {
                                                $tingkat = 'Belum ditentukan';
                                            }
                                        }
                                    }
                                @endphp
                                {{ $tingkat }}
                            @endif
                        </p>
                    </div>
                    <hr>
                    <div>
                        <label class="text-xs font-weight-bold text-primary text-uppercase mb-2 d-block">Walikelas</label>
                        <p class="h6 font-weight-bold text-gray-800">
                            <td>{{ $waliKelas }}</td>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Statistik Jadwal -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar"></i> Statistik Jadwal
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $totalJadwal = $jadwal->count();
                        $hariUnik = $jadwal->pluck('hari')->unique()->count();
                        $mapelUnik = $jadwal->pluck('mata_pelajaran_id')->unique()->count();
                    @endphp

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-left-primary shadow mb-3">
                                <div class="card-body p-3">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Jadwal</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalJadwal }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-left-success shadow mb-3">
                                <div class="card-body p-3">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Hari Efektif</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $hariUnik }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card border-left-info shadow">
                                <div class="card-body p-3">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Mata Pelajaran</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $mapelUnik }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jadwal Hari Ini (Jika ada) -->
            @if($jadwalHariIni->count() > 0)
            <div class="card shadow border-left-success">
                <div class="card-header py-3 bg-success">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-calendar-day"></i> Jadwal Hari Ini
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($jadwalHariIni as $j)
                    <div class="mb-3">
                        <div class="small text-gray-500 mb-1">
                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $j->jam_mulai)->format('H:i') }} -
                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $j->jam_selesai)->format('H:i') }}
                        </div>
                        <p class="font-weight-bold mb-1">{{ $j->mapel->nama_mapel ?? '-' }}</p>
                        <small class="text-muted">Guru: {{ $j->guru->user->name ?? '-' }}</small>
                    </div>
                    <hr>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    @endsection
