<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('DashboardSiswa') }}">
        <div class="sidebar-brand-icon">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <div class="sidebar-brand-text mx-1">Sistem Akademik</div>
    </a>
        <div class="sidebar-heading sidebar-brand-text text-lg-center mb-2">SMK Negeri 1 Cipeundeuy</div>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('DashboardSiswa') }}">
            <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
        </a>
    </li>

    <!-- Nav Item - Utilities Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-fw fa-chart-bar"></i><span>Lihat Data</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Menu Data :</h6>
                <a class="collapse-item" href="{{ route('Nilai') }}">Nilai</a>
                <a class="collapse-item" href="{{ route('Kehadiran') }}">Kehadiran</a>
                <a class="collapse-item" href="{{ route('JadwalPelajaran') }}">Jadwal Pelajaran</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route ('LaporanAkademikSiswa') }}">
            <i class="fas fa-fw fa-file-invoice"></i><span>Laporan Akademik</span>
        </a>
    </li>

</ul>
