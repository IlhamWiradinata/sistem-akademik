@extends('layouts.app')

@section('title', 'Lupa Password - SMK Negeri 1 Cipeundeuy')

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

        <h2 class="login-title">Lupa Password</h2>

        @if (session('success'))
        <div class="floating-alert-container">
            <div class="floating-alert success">
                <div class="alert-icon">
                    <svg width="20" height="20" viewBox="0 0 20 20">
                        <path d="M10 0C4.48 0 0 4.48 0 10C0 15.52 4.48 20 10 20C15.52 20 20 15.52 20 10C20 4.48 15.52 0 10 0ZM8 15L3 10L4.41 8.59L8 12.17L15.59 4.58L17 6L8 15Z" fill="currentColor"/>
                    </svg>
                </div>
                <div class="alert-content">
                    <h4 class="alert-title">Berhasil</h4>
                    <p>{{ session('success') }}</p>
                </div>
                <button class="alert-close" onclick="this.closest('.floating-alert').remove()">
                    <svg width="16" height="16" viewBox="0 0 16 16">
                        <path d="M12 4L4 12M4 4L12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
        </div>
        @endif

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
            Masukkan alamat email Anda yang terdaftar.<br>
            Kami akan mengirimkan link untuk mereset password Anda.
        </p>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email"
                       value="{{ old('email') }}"
                       placeholder="Masukkan email Anda" required>
            </div>

            <button type="submit" class="login-button">Kirim Link Reset Password</button>

            <div class="text-center mt-3">
                <a href="{{ route('login') }}" style="color: #4e73df; text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Kembali ke Login
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
