@extends('Layouts.app')

@section('title', 'Login Akademik - SMK Negeri 1 Cipeundeuy')

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

        <h2 class="login-title">Log In Akademik</h2>

        @if ($errors->any())
        <div class="floating-alert-container" id="floatingAlert">
            <div class="floating-alert error">
                <div class="alert-icon">
                    <svg width="20" height="20" viewBox="0 0 20 20">
                        <path d="M10 0C4.48 0 0 4.48 0 10C0 15.52
                        4.48 20 10 20C15.52 20 20 15.52 20 10C20 4.48
                        15.52 0 10 0ZM11 15H9V13H11V15ZM11 11H9V5H11V11Z"
                        fill="currentColor"/>
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
                        <path d="M12 4L4 12M4 4L12 12" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        @if (session('success'))
        <div class="floating-alert-container" id="floatingAlert">
            <div class="floating-alert success">
                <div class="alert-icon">
                    <svg width="20" height="20" viewBox="0 0 20 20">
                        <path d="M10 0C4.48 0 0 4.48 0 10C0 15.52
                        4.48 20 10 20C15.52 20 20 15.52 20 10C20 4.48
                        15.52 0 10 0ZM8 15L3 10L4.41 8.59L8 12.17L15.59
                        4.58L17 6L8 15Z" fill="currentColor"/>
                    </svg>
                </div>
                <div class="alert-content">
                    <h4 class="alert-title">Berhasil</h4>
                    <p>{{ session('success') }}</p>
                </div>
                <button class="alert-close" onclick="this.closest('.floating-alert').remove()">
                    <svg width="16" height="16" viewBox="0 0 16 16">
                        <path d="M12 4L4 12M4 4L12 12" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        <p class="welcome-message">
            Selamat Datang, silakan masuk menggunakan<br>
            akun anda untuk melanjutkan
        </p>

        <form id="loginForm" method="post" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" value="{{ old('email') }}"
                       id="email" name="email" placeholder="Email">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••">
            </div>

            <button type="submit" class="login-button">Masuk</button>

            <div class="text-center">
                <a href="{{ route('forgot-password') }}"
                   style="color: #4e73df; text-decoration: none; font-size: 14px;">
                    Lupa Kata Sandi?
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
.floating-alert.success {
    background: #d4edda;
    border-left: 4px solid #28a745;
}
.floating-alert.success .alert-icon {
    color: #28a745;
}
.floating-alert.success .alert-title {
    color: #155724;
}
.floating-alert.success p {
    color: #155724;
}
.mt-3 {
    margin-top: 1rem;
}
.text-center {
    text-align: center;
}
</style>
@endpush
