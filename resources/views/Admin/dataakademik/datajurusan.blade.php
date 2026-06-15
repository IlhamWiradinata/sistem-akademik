@extends('Layouts.LayoutsAdmin.app')

@section('title')
<title> Data Jurusan - Sistem Akademik </title>
@endsection

@section('content')
{{-- PAGE HEADING --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h4 class="h4 mb-0 text-gray-800">
        <i class="fas fa-graduation-cap mr-2"></i> Data Jurusan
    </h4>
</div>

{{-- Statistik Card --}}
<div class="row">
     <!-- Total Jurusan -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Jurusan
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalJurusan }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-graduation-cap fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Action Button --}}
<div class="mb-3 d-flex justify-content-between align-items-center">
    {{-- Tombol Tambah --}}
    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambahJurusan">
        <i class="fas fa-plus"></i> Tambah Jurusan
    </button>
    {{-- Tombol Export --}}
    <div>
        <a href="{{ route('jurusan.export') }}" target="_blank" class="btn btn-danger btn-sm">
            <i class="fas fa-file-pdf"></i> PDF
        </a>
    </div>
</div>

{{-- Tabel Jurusan --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table"></i> Daftar Jurusan
        </h6>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
            <thead class="thead-light">
                <tr class="text-center">
                    <th>Kode Jurusan</th>
                    <th>Nama Jurusan</th>
                    <th>Keterangan</th>
                    <th width="10%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jurusan as $j)
                <tr>
                    <td>{{ $j->kode_jurusan }}</td>
                    <td>{{ $j->nama_jurusan }}</td>
                    <td>{{ $j->keterangan ?? '-' }}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="editJurusan({{ $j->id }})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('jurusan.delete', $j->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus jurusan ini?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambahJurusan" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('jurusan.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Jurusan</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label>Kode Jurusan</label>
                    <input type="text" name="kode_jurusan" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Nama Jurusan</label>
                    <input type="text" name="nama_jurusan" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="modalEditJurusan" tabindex="-1">
    <div class="modal-dialog">
        <form id="formEditJurusan" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Edit Jurusan</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label>Kode Jurusan</label>
                    <input type="text" name="kode_jurusan" id="edit_kode_jurusan" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Nama Jurusan</label>
                    <input type="text" name="nama_jurusan" id="edit_nama_jurusan" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Keterangan</label>
                    <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
<script>
    function editJurusan(id){
        $.get("/admin/jurusan/edit/" + id, function(data){
            $('#edit_kode_jurusan').val(data.kode_jurusan);
            $('#edit_nama_jurusan').val(data.nama_jurusan);
            $('#edit_keterangan').val(data.keterangan);
            $('#formEditJurusan').attr('action', '/admin/jurusan/update/' + id);
            $('#modalEditJurusan').modal('show');
        });
    }
</script>
@endsection
