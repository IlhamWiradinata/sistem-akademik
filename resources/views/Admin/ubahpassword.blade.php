@extends('Layouts.LayoutsAdmin.app')

@section('title')
<title> Sistem Akademik - Ganti Password </title>
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-key fa-fw"></i> Ganti Password
    </h1>
    <a href="{{ route('ProfileAdmin') }}" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Profil
    </a>
</div>

<!-- Content Row -->
<div class="row justify-content-center">
    <div class="col-xl-6 col-lg-8 col-md-10">
        <!-- Change Password Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-lock"></i> Form Ganti Password
                </h6>
            </div>
            <div class="card-body">
                <!-- Success Message -->
                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-check-circle"></i> Berhasil!</strong>
                    <p class="mb-0">{{ session('success') }}</p>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif

                <!-- Error Messages -->
                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-exclamation-triangle"></i> Terjadi Kesalahan!</strong>
                    <ul class="mb-0 mt-2 pl-3">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif

                <!-- Info Alert -->
                <div class="alert alert-info border-left-info" role="alert">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                        <i class="fas fa-info-circle"></i> Informasi
                    </div>
                    <div class="text-xs">
                        Password baru harus minimal 8 karakter dan mengandung kombinasi huruf serta angka untuk keamanan yang lebih baik.
                    </div>
                </div>

                <!-- Change Password Form -->
                <form method="POST" action="{{ route('update.password') }}" id="changePasswordForm">
                    @csrf

                    <!-- Current Password -->
                    <div class="form-group">
                        <label for="current_password" class="font-weight-bold">
                            Password Saat Ini <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </div>
                            <input type="password"
                                   name="current_password"
                                   id="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   placeholder="Masukkan password saat ini"
                                   required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr>

                    <!-- New Password -->
                    <div class="form-group">
                        <label for="password" class="font-weight-bold">
                            Password Baru <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-key"></i>
                                </span>
                            </div>
                            <input type="password"
                                   name="password"
                                   id="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Masukkan password baru (min. 8 karakter)"
                                   required
                                   minlength="8">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- Password Strength Indicator -->
                        <div class="progress mt-2" style="height: 5px;">
                            <div id="password-strength" class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <small id="password-strength-text" class="form-text text-muted"></small>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="password_confirmation" class="font-weight-bold">
                            Konfirmasi Password Baru <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                            </div>
                            <input type="password"
                                   name="password_confirmation"
                                   id="password_confirmation"
                                   class="form-control"
                                   placeholder="Ketik ulang password baru"
                                   required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password_confirmation">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <small id="password-match-text" class="form-text"></small>
                    </div>

                    <hr>

                    <!-- Action Buttons -->
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-success btn-block" id="submitBtn">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('ProfileAdmin') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Security Tips Card -->
        <div class="card shadow mb-4 border-left-warning">
            <div class="card-body">
                <h6 class="font-weight-bold text-warning mb-3">
                    <i class="fas fa-shield-alt"></i> Tips Keamanan Password
                </h6>
                <ul class="text-xs mb-0 pl-3">
                    <li>Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol</li>
                    <li>Hindari menggunakan informasi pribadi yang mudah ditebak</li>
                    <li>Jangan gunakan password yang sama untuk berbagai akun</li>
                    <li>Ganti password secara berkala setiap 3-6 bulan</li>
                    <li>Jangan membagikan password kepada siapa pun</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle password visibility
    $('.toggle-password').click(function() {
        const targetId = $(this).data('target');
        const input = $('#' + targetId);
        const icon = $(this).find('i');

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Password strength checker
    $('#password').on('input', function() {
        const password = $(this).val();
        const strengthBar = $('#password-strength');
        const strengthText = $('#password-strength-text');

        let strength = 0;
        let message = '';
        let barClass = '';

        if (password.length >= 8) strength++;
        if (password.match(/[a-z]+/)) strength++;
        if (password.match(/[A-Z]+/)) strength++;
        if (password.match(/[0-9]+/)) strength++;
        if (password.match(/[$@#&!]+/)) strength++;

        switch(strength) {
            case 0:
            case 1:
                message = 'Password sangat lemah';
                barClass = 'bg-danger';
                break;
            case 2:
                message = 'Password lemah';
                barClass = 'bg-warning';
                break;
            case 3:
                message = 'Password cukup';
                barClass = 'bg-info';
                break;
            case 4:
                message = 'Password kuat';
                barClass = 'bg-success';
                break;
            case 5:
                message = 'Password sangat kuat';
                barClass = 'bg-success';
                break;
        }

        const percentage = (strength / 5) * 100;
        strengthBar.css('width', percentage + '%')
                   .removeClass('bg-danger bg-warning bg-info bg-success')
                   .addClass(barClass);
        strengthText.text(message).removeClass('text-danger text-warning text-info text-success')
                    .addClass(barClass.replace('bg-', 'text-'));
    });

    // Password match checker
    $('#password_confirmation').on('input', function() {
        const password = $('#password').val();
        const confirmation = $(this).val();
        const matchText = $('#password-match-text');

        if (confirmation.length > 0) {
            if (password === confirmation) {
                matchText.text('✓ Password cocok').removeClass('text-danger').addClass('text-success');
            } else {
                matchText.text('✗ Password tidak cocok').removeClass('text-success').addClass('text-danger');
            }
        } else {
            matchText.text('');
        }
    });

    // Form validation
    $('#changePasswordForm').submit(function(e) {
        const password = $('#password').val();
        const confirmation = $('#password_confirmation').val();

        if (password !== confirmation) {
            e.preventDefault();
            alert('Password dan konfirmasi password tidak cocok!');
            return false;
        }

        if (password.length < 8) {
            e.preventDefault();
            alert('Password minimal 8 karakter!');
            return false;
        }

        // Disable submit button to prevent double submission
        $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endpush
