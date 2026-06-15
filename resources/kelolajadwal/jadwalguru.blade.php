@extends('layouts.layoutsadmin.app')

@section('title')
<title>Sistem Akademik - Jadwal Guru</title>
@endsection

@section('content')

<h4 class="mb-4 text-gray-800"><i class="fas fa-user-tie"></i> Jadwal Mengajar Guru</h4>

{{-- FILTER GURU --}}
<form method="GET" action="{{ route('jadwal.guru') }}" class="mb-4">
    <div class="row">
        <div class="col-md-4">
            <select name="guru_id" class="form-control" onchange="this.form.submit()">
                <option value="">-- Pilih Guru --</option>
                @foreach($guru as $g)
                    <option value="{{ $g->id }}" {{ request('guru_id') == $g->id ? 'selected' : '' }}>
                        {{ $g->user->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</form>

{{-- TABEL --}}
<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <strong>Jadwal Mengajar</strong>
    </div>

    <div class="card-body">
        @if($jadwal->count())
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-light">
                <tr class="text-center">
                    <th>Hari</th>
                    <th>Jam</th>
                    <th>Mata Pelajaran</th>
                    <th>Kelas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jadwal as $j)
                <tr>
                    <td>{{ $j->hari }}</td>
                    <td>{{ $j->jam_mulai }} - {{ $j->jam_selesai }}</td>
                    <td>{{ $j->mapel->nama_mapel ?? '-' }}</td>
                    <td>{{ $j->kelas->nama_kelas ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @else
        <p class="text-center text-muted">Silakan pilih guru untuk melihat jadwal.</p>
        @endif
    </div>
</div>

@endsection
