@extends('layouts.layoutsadmin.app')

@section('title')
<title>Hasil Pencarian - Sistem Akademik</title>
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-search"></i> Hasil Pencarian
    </h1>
    <span class="badge badge-primary">
        <i class="fas fa-info-circle"></i> {{ $totalResults }} hasil ditemukan
    </span>
</div>

<div class="row">
    <div class="col-xl-12">
        <!-- Search Info -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-filter"></i> Kata Kunci: "{{ $query }}"
                </h6>
            </div>
            <div class="card-body">
                @if($totalResults == 0)
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                        <h5 class="font-weight-bold">Tidak ada hasil ditemukan</h5>
                        <p class="mb-0">Tidak ada data yang cocok dengan pencarian "{{ $query }}"</p>
                    </div>
                @else

                    <!-- Users Results -->
                    @if($user->count() > 0)
                    <div class="card border-left-primary mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-users"></i> Pengguna ({{ $user->count() }})
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($user as $index => $user)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <i class="fas fa-user-circle text-primary"></i>
                                                <strong>{{ $user->name }}</strong>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <!-- Tampilkan role dari kolom role di tabel users -->
                                                <span class="badge badge-info">{{ $user->role ?? 'User' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $user->is_active ? 'success' : 'danger' }}">
                                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Siswa Results -->
                    @if($siswa->count() > 0)
                    <div class="card border-left-success mb-4">
                        <div class="card-header bg-success text-white">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-user-graduate"></i> Siswa ({{ $siswa->count() }})
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th>NISN</th>
                                            <th>Nama Siswa</th>
                                            <th>Jurusan</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($siswa as $index => $s)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <span class="badge badge-secondary">{{ $s->nisn }}</span>
                                            </td>
                                            <td>
                                                <i class="fas fa-user text-success"></i>
                                                <strong>{{ $s->user->name }}</strong>
                                            </td>
                                            <td>{{ $s->jurusan->nama_jurusan ?? '-' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $s->status == 'aktif' ? 'success' : 'danger' }}">
                                                    {{ $s->status == 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Guru Results -->
                    @if($guru->count() > 0)
                    <div class="card border-left-warning mb-4">
                        <div class="card-header bg-warning text-white">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-chalkboard-teacher"></i> Guru ({{ $guru->count() }})
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th>NIP</th>
                                            <th>Nama Guru</th>
                                            <th>Bidang Keahlian</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($guru as $index => $g)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <span class="badge badge-info">{{ $g->nip }}</span>
                                            </td>
                                            <td>
                                                <i class="fas fa-user-tie text-warning"></i>
                                                <strong>{{ $g->user->name }}</strong>
                                            </td>
                                            <td>{{ $g->bidang_keahlian }}</td>
                                            <td>
                                                <span class="badge badge-{{ $g->status == 'aktif' ? 'success' : 'danger' }}">
                                                    {{ $g->status == 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Kelas Results -->
                    @if($kelas->count() > 0)
                    <div class="card border-left-info mb-4">
                        <div class="card-header bg-info text-white">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-school"></i> Kelas ({{ $kelas->count() }})
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($kelas as $k)
                                <div class="col-md-4 mb-3">
                                    <div class="card border-info h-100">
                                        <div class="card-body">
                                            <h6 class="card-title text-info">
                                                <i class="fas fa-door-open"></i> {{ $k->nama_kelas }}
                                            </h6>
                                            <p class="mb-1">
                                                <small class="text-muted">Jurusan:</small>
                                                {{ $k->jurusan->nama_jurusan ?? '-' }}
                                            </p>
                                            <p class="mb-1">
                                                <small class="text-muted">Tahun Ajaran:</small>
                                                {{ $k->tahun_ajaran }}
                                            </p>
                                            <p class="mb-0">
                                                <small class="text-muted">Tingkat:</small>
                                                {{ $k->tingkat ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Jurusan Results -->
                    @if(isset($jurusan) && $jurusan->count() > 0)
                    <div class="card border-left-secondary mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-graduation-cap"></i> Jurusan ({{ $jurusan->count() }})
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($jurusan as $j)
                                <div class="col-md-3 mb-3">
                                    <div class="card border-secondary h-100">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">
                                                <i class="fas fa-book-reader"></i>
                                                {{ $j->nama_jurusan }}
                                            </h6>
                                            <p class="mb-0">
                                                <small class="text-muted">Kode:</small>
                                                {{ $j->kode_jurusan ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                @endif

                <!-- Back Button -->
                <div class="text-center mt-4">
                    <a href="{{ route('DashboardAdmin') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                    </a>
                    <a href="javascript:history.back()" class="btn btn-secondary">
                        <i class="fas fa-reply"></i> Kembali ke Halaman Sebelumnya
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
