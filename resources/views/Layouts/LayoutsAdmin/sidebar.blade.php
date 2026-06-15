<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('DashboardAdmin') }}">
        <div class="sidebar-brand-icon">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <div class="sidebar-brand-text mx-1">Sistem Akademik</div>
    </a>

    <div class="sidebar-heading sidebar-brand-text text-lg-center mb-2">
        SMK Negeri 1 Cipeundeuy
    </div>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('DashboardAdmin') }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Heading -->
    <div class="sidebar-heading">
        Kelola Data Akademik
    </div>

    <!-- Kelola Kelas, Mapel, Jurusan -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseDataAkademik"
            aria-expanded="true" aria-controls="collapseDataAkademik">
            <i class="fas fa-school"></i>
            <span>Data Akademik</span>
        </a>
        <div id="collapseDataAkademik" class="collapse" aria-labelledby="headingDataAkademik"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Kelola Data :</h6>
                <a class="collapse-item" href="{{ route('DataKelas') }}">Kelas</a>
                <a class="collapse-item" href="{{ route('DataMapel') }}">Mata Pelajaran</a>
                <a class="collapse-item" href="{{ route('DataJurusan') }}">Jurusan</a>
                <a class="collapse-item" href="{{ route('KelolaJadwal') }}">Kelola Jadwal</a>
            </div>
        </div>
    </li>

    <!-- Monitoring Siswa -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMonitoring"
            aria-expanded="true" aria-controls="collapseMonitoring">
            <i class="fas fa-fw fa-chart-bar"></i>
            <span>Monitoring Siswa</span>
        </a>
        <div id="collapseMonitoring" class="collapse" aria-labelledby="headingMonitoring"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Menu Monitoring :</h6>
                <a class="collapse-item" href="{{ route('MonitoringNilai') }}">Nilai</a>
                <a class="collapse-item" href="{{ route('MonitoringKehadiran') }}">Kehadiran</a>
            </div>
        </div>
    </li>

    <!-- Laporan Akademik -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('LaporanAkademikAdmin') }}">
            <i class="fas fa-fw fa-file-invoice"></i>
            <span>Laporan Akademik</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Analisis Prestasi
    </div>

    <!-- Siswa Berprestasi (Decision Tree) -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('prestasi.index') }}">
            <i class="fas fa-trophy"></i>
            <span>Siswa Berprestasi</span>
        </a>
    </li>

    <!-- Heading -->
    <div class="sidebar-heading">
        Kelola Akun
    </div>

    <!-- Data Master -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAkun"
            aria-expanded="true" aria-controls="collapseAkun">
            <i class="fas fa-users-cog"></i>
            <span>Data Master</span>
        </a>
        <div id="collapseAkun" class="collapse" aria-labelledby="headingAkun" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('dataMaster.admin') }}">Admin & Kurikulum</a>
                <a class="collapse-item" href="{{ route('dataMaster.guru') }}">Guru</a>
                <a class="collapse-item" href="{{ route('dataMaster.siswa') }}">Siswa</a>
                <a class="collapse-item" href="{{ route('admin.session') }}">Session</a>
            </div>
        </div>
    </li>

</ul>
<!-- End of Sidebar -->
