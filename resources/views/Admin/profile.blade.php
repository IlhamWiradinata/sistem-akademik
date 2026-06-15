@extends('layouts.layoutsadmin.app')

@section('title')
<title>Sistem Akademik - Profil Admin</title>
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-user-circle fa-fw"></i> Profil Administrator
    </h1>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Profile Information Card -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-id-card"></i> Data Profil Administrator
                </h6>
                <div>
                    <button id="editBtn" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Edit Profil
                    </button>
                    <button id="saveBtn" type="submit" form="profile-form" class="btn btn-success btn-sm d-none">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <button id="cancelBtn" type="button" class="btn btn-secondary btn-sm d-none">
                        <i class="fas fa-times"></i> Batal
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif

                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan:
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif

                <form id="profile-form" action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Informasi Pribadi -->
                    <h6 class="font-weight-bold text-primary mb-3">
                        <i class="fas fa-user"></i> Informasi Pribadi
                    </h6>
                    <hr class="mt-0 mb-3">

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label font-weight-bold">Nama Lengkap</label>
                        <div class="col-sm-9">
                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label font-weight-bold">Email</label>
                        <div class="col-sm-9">
                            <input type="email" name="email" class="form-control" value="{{ $user->email }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label font-weight-bold">NIP</label>
                        <div class="col-sm-9">
                            <input type="text" name="nip" class="form-control" value="{{ $profile->nip }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label font-weight-bold">Jabatan</label>
                        <div class="col-sm-9">
                            <input type="text" name="jabatan" class="form-control" value="{{ $profile->jabatan }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label font-weight-bold">No HP</label>
                        <div class="col-sm-9">
                            <input type="text" name="no_hp" class="form-control" value="{{ $profile->no_hp }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label font-weight-bold">Alamat</label>
                        <div class="col-sm-9">
                            <textarea name="alamat" class="form-control" rows="3" readonly>{{ $profile->alamat }}</textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Profile Photo & Password Card -->
    <div class="col-xl-4 col-lg-5">
        <!-- Profile Photo Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-image"></i> Foto Profil
                </h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <img id="preview"
                        src="{{ $user->adminProfile && $user->adminProfile->photo ? asset('storage/' . $user->adminProfile->photo) : asset('images/default-profile.png') }}"
                        alt="Foto Profil"
                        class="img-thumbnail rounded-circle"
                        style="width: 150px; height: 150px; object-fit: cover;">
                </div>
                <button type="button" id="changePhotoBtn" class="btn btn-primary btn-sm d-none">
                    <i class="fas fa-camera"></i> Ganti Foto
                </button>
                <input type="file" id="photo" name="photo" form="profile-form" class="d-none" accept="image/*">
                <p class="text-muted small mt-2 mb-0">Format: JPG, PNG (Max 2MB)</p>
            </div>
        </div>

        <!-- Info Card -->
        <div class="card shadow mb-4 border-left-info">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                    <i class="fas fa-info-circle"></i> Informasi
                </div>
                <div class="text-xs text-gray-800">
                    Pastikan data profil Anda selalu akurat dan terbaru. Hubungi admin jika ada kesalahan data.
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
$(document).ready(function() {
    const editBtn = $('#editBtn');
    const saveBtn = $('#saveBtn');
    const cancelBtn = $('#cancelBtn');
    const changePhotoBtn = $('#changePhotoBtn');
    const photoInput = $('#photo');
    const preview = $('#preview');
    const formInputs = $('#profile-form input[type="text"], #profile-form input[type="date"], #profile-form textarea');
    const excludedInputs = 'input[name="name"], input[name="email"], input[name="nip"], input:hidden';

    // Edit button click
    editBtn.click(function() {
        editBtn.addClass('d-none');
        saveBtn.removeClass('d-none');
        cancelBtn.removeClass('d-none');
        changePhotoBtn.removeClass('d-none');

        formInputs.not(excludedInputs).prop('readonly', false).addClass('border-primary');
    });

    // Cancel button click
    cancelBtn.click(function() {
        location.reload();
    });

    // Change photo button click
    changePhotoBtn.click(function() {
        photoInput.click();
    });

    // Photo input change
    photoInput.change(function() {
        const file = this.files[0];
        if (file) {
            // Validate file size (2MB max)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file maksimal 2MB!');
                this.value = '';
                return;
            }

            // Validate file type
            if (!file.type.match('image.*')) {
                alert('File harus berupa gambar!');
                this.value = '';
                return;
            }

            // Preview image
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});

$(document).ready(function() {
    const editBtn = $('#editBtn');
    const saveBtn = $('#saveBtn');
    const cancelBtn = $('#cancelBtn');
    const jurusanSelect = $('#jurusanSelect');
    const kelasSelect = $('#kelasSelect');

    const formInputs = $('#profile-form input[type="text"], #profile-form input[type="date"], #profile-form textarea');

    // Edit button click
    editBtn.click(function() {
        editBtn.addClass('d-none');
        saveBtn.removeClass('d-none');
        cancelBtn.removeClass('d-none');

        formInputs.prop('readonly', false).addClass('border-primary');

        jurusanSelect.prop('disabled', false);
        kelasSelect.prop('disabled', false);
    });

    jurusanSelect.change(function() {
    const jurusanId = $(this).val();

    kelasSelect.find('option').each(function() {
        const option = $(this);
        if(option.val() === "") return; // biarkan placeholder
        if(option.data('jurusan') == jurusanId){
            option.show();
        } else {
            option.hide();
        }
    });

    // Reset pilihan kelas
    kelasSelect.val('');
    });


    // Cancel button
    cancelBtn.click(function() {
        location.reload();
    });
});
</script>
@endpush
