@extends('layouts.app')

@section('title', 'Reset Password - SMK Negeri 1 Cipeundeuy')

@section('content')
<div class="main-container">
    <div class="left-panel">
        <div class="overlay"></div>
        <div class="school-info">
            <img src="{{ asset('template/img/Logo.png') }}" alt="Logo SMK Negeri 1 Cipeundeuy" class="school-logo">
            <h2>SMK Negeri 1 Cipeundeuy</h2>
            <p>Sistem Informasi Akademik</p>
        </div>
    </div>

    <div class="right-panel">
        <div class="header-image">
            <img src="{{ asset('template/img/Thumbnail.jpeg') }}" alt="SMK Negeri 1 Cipeundeuy" class="header-logo">
        </div>

        <h2 class="login-title">Reset Password</h2>

        @if (session('error'))
        <div class="floating-alert-container">
            <div class="floating-alert error">
                <div class="alert-icon">
                    <svg width="20" height="20" viewBox="0 0 20 20">
                        <path d="M10 0C4.48 0 0 4.48 0 10C0 15.52 4.48 20 10 20C15.52 20 20 15.52 20 10C20 4.48 15.52 0 10 0ZM11 15H9V13H11V15ZM11 11H9V5H11V11Z" fill="currentColor"/>
                    </svg>
                </div>
                <div class="alert-content">
                    <h4 class="alert-title">Terjadi Kesalahan</h4>
                    <p>{{ session('error') }}</p>
                </div>
                <button class="alert-close" onclick="this.closest('.floating-alert').remove()">
                    <svg width="16" height="16" viewBox="0 0 16 16">
                        <path d="M12 4L4 12M4 4L12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        @if ($errors->any())
        <div class="floating-alert-container">
            <div class="floating-alert error">
                <div class="alert-icon">
                    <svg width="20" height="20" viewBox="0 0 20 20">
                        <path d="M10 0C4.48 0 0 4.48 0 10C0 15.52 4.48 20 10 20C15.52 20 20 15.52 20 10C20 4.48 15.52 0 10 0ZM11 15H9V13H11V15ZM11 11H9V5H11V11Z" fill="currentColor"/>
                    </svg>
                </div>
                <div class="alert-content">
                    <h4 class="alert-title">Terjadi Kesalahan</h4>
                    <ul class="alert-list">
                        @foreach ($errors->all() as $item)
                        <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
                <button class="alert-close" onclick="this.closest('.floating-alert').remove()">
                    <svg width="16" height="16" viewBox="0 0 16 16">
                        <path d="M12 4L4 12M4 4L12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        <p class="welcome-message">
            Buat password baru untuk akun Anda.<br>
            Pastikan password minimal 8 karakter.
        </p>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email"
                       value="{{ $email ?? old('email') }}"
                       readonly
                       class="form-control"
                       style="background-color: #f8f9fc; cursor: not-allowed;">
            </div>

            <div class="form-group">
                <label for="password">Password Baru</label>
                <input type="password" name="password" id="password"
                       placeholder="Minimal 8 karakter" required>
                <small style="color: #6c757d; display: block; margin-top: 5px;">
                    Minimal 8 karakter
                </small>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password</label>
                <input type="password" name="password_confirmation"
                       id="password_confirmation"
                       placeholder="Ulangi password baru" required>

            </div>

            <button type="submit" class="login-button">Reset Password</button>
        </form>
    </div>
</div>
@endsection
