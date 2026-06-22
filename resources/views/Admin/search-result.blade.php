@extends('Layouts.LayoutsAdmin.app')

@section('title')
<title> Hasil Pencarian - Sistem Akademik </title>
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0 text-gray-800">
        <i class="fas fa-search"></i> Hasil Pencarian untuk: "{{ $query }}"
    </h4>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card shadow mb-4">
            <div class="card-body">

                @if($user->isEmpty() && $siswa->isEmpty() && $guru->isEmpty() && $kelas->isEmpty())
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-info-circle"></i> Tidak ada hasil untuk "{{ $query }}"
                    </div>
                @else
                    <h6 class="text-primary">Hasil dari Tabel User</h6>
                    <ul>
                        @foreach ($user as $u)
                            <li>{{ $u->name }} ({{ $u->email }})</li>
                        @endforeach
                    </ul>

                    <h6 class="text-primary mt-3">Hasil dari Tabel Siswa</h6>
                    <ul>
                        @foreach ($siswa as $s)
                            <li>{{ $s->nisn }} - {{ $s->nama_siswa }}</li>
                        @endforeach
                    </ul>

                    <h6 class="text-primary mt-3">Hasil dari Tabel Guru</h6>
                    <ul>
                        @foreach ($guru as $g)
                            <li>{{ $g->nip }} - {{ $g->mata_pelajaran }}</li>
                        @endforeach
                    </ul>

                    <h6 class="text-primary mt-3">Hasil dari Tabel Kelas</h6>
                    <ul>
                        @foreach ($kelas as $k)
                            <li>{{ $k->nama_kelas }}</li>
                        @endforeach
                    </ul>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
