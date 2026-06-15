@extends('layouts.layoutsadmin.app')

@section('title')
<title>Sistem Akademik - Jadwal Kelas</title>
@endsection

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0 text-gray-800">
        <i class="fas fa-calendar-alt mr-2"></i> Jadwal Kelas
    </h4>
</div>

{{-- PILIH KELAS --}}
<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white py-3">
        <h6 class="m-0 font-weight-bold">Pilih Kelas</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('jadwal.kelas') }}" method="GET">
            <div class="form-group col-md-4 p-0">
                <select name="kelas_id" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

{{-- TABEL JADWAL --}}
@if(isset($jadwal) && count($jadwal) > 0)
<div class="card shadow">
    <div class="card-header bg-primary text-white py-3">
        <h6 class="m-0 font-weight-bold">
            Jadwal Kelas: {{ $kelas_nama }}
        </h6>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover shadow-sm">
                <thead class="thead-light">
                    <tr class="text-center">
                        <th>#</th>
                        <th>Hari</th>
                        <th>Jam</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jadwal as $j)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $j->hari }}</td>
                        <td>{{ $j->jam_mulai }} - {{ $j->jam_selesai }}</td>
                        <td>{{ $j->mapel->nama_mapel }}</td>
                        <td>{{ $j->guru->user->name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@elseif(request('kelas_id'))
<div class="alert alert-warning">
    <i class="fas fa-info-circle"></i> Tidak ada jadwal untuk kelas ini.
</div>
@endif

@endsection
