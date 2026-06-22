@extends('Layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-search"></i> Hasil Pencarian
        </h1>
        <span class="badge badge-primary">
            {{ $totalResults }} hasil ditemukan
        </span>
    </div>

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
                    <h5>Tidak ada hasil ditemukan</h5>
                </div>
            @else
                <!-- Siswa di Kelas -->
                @if($siswa->count() > 0)
                <div class="mb-4">
                    <h5 class="text-success">
                        <i class="fas fa-user-graduate"></i> Siswa ({{ $siswa->count() }})
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>NISN</th>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($siswa as $s)
                                <tr>
                                    <td>{{ $s->nisn }}</td>
                                    <td>{{ $s->user->name }}</td>
                                    <td>{{ $s->kelas->nama_kelas ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Kelas -->
                @if($kelas->count() > 0)
                <div class="mb-4">
                    <h5 class="text-info">
                        <i class="fas fa-school"></i> Kelas ({{ $kelas->count() }})
                    </h5>
                    <div class="row">
                        @foreach($kelas as $k)
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6>{{ $k->nama_kelas }}</h6>
                                    <p class="mb-1">Jurusan: {{ $k->jurusan->nama_jurusan ?? '-' }}</p>
                                    <p class="mb-0">Tahun: {{ $k->tahun_ajaran }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
