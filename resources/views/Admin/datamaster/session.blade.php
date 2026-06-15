@extends('Layouts.LayoutsAdmin.app')

@section('title')
<title>Sistem Akademik - Session Aktif</title>
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-user-clock"></i> Session Aktif
    </h1>
    <button class="btn btn-primary shadow-sm" onclick="location.reload()">
        <i class="fas fa-sync-alt fa-sm"></i> Refresh
    </button>
</div>

<!-- Info Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Session</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Aktif (5 Menit)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['aktif'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-circle fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Idle (15 Menit)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['idle'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-circle fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Session Hari Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['today'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-day fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Session Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table"></i> Daftar Session Aktif
        </h6>
        <span class="badge badge-primary badge-pill">{{ $sessions->count() }} Session</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr class="text-center">
                        <th width="5%">No</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>IP Address</th>
                        <th>User Agent</th>
                        <th>Terakhir Aktif</th>
                        <th>Durasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $key => $session)
                    <tr>
                        <td class="text-center">{{ $key + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="mr-2">
                                    @if($session->role == 'Administrator')
                                        <i class="fas fa-user-shield text-primary"></i>
                                    @elseif($session->role == 'Guru')
                                        <i class="fas fa-chalkboard-teacher text-warning"></i>
                                    @elseif($session->role == 'Siswa')
                                        <i class="fas fa-user-graduate text-success"></i>
                                    @else
                                        <i class="fas fa-user text-secondary"></i>
                                    @endif
                                </div>
                                <div>
                                    <strong>{{ $session->name ?? 'Guest' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $session->email ?? '-' }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            @if($session->role)
                                <span class="badge
                                    @if($session->role == 'Administrator') badge-primary
                                    @elseif($session->role == 'Guru') badge-warning
                                    @elseif($session->role == 'Siswa') badge-success
                                    @else badge-secondary @endif">
                                    {{ $session->role }}
                                </span>
                            @else
                                <span class="badge badge-secondary">Guest</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($session->status == 'aktif')
                                <span class="badge badge-success">
                                    <i class="fas fa-circle fa-xs"></i> Aktif
                                </span>
                            @elseif($session->status == 'idle')
                                <span class="badge badge-warning">
                                    <i class="fas fa-circle fa-xs"></i> Idle
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-circle fa-xs"></i> Offline
                                </span>
                            @endif
                        </td>
                        <td>
                            <code>{{ $session->ip_address }}</code>
                        </td>
                        <td>
                            <small>{{ $session->user_agent_short }}</small>
                            @if(strlen($session->user_agent) > 60)
                                <br>
                                <button class="btn btn-sm btn-link p-0"
                                        type="button"
                                        data-toggle="collapse"
                                        data-target="#agent{{ $key }}">
                                    Lihat lengkap
                                </button>
                                <div class="collapse" id="agent{{ $key }}">
                                    <small class="text-muted">{{ $session->user_agent }}</small>
                                </div>
                            @endif
                        </td>
                        <td>
                            {{ $session->last_active->format('d/m/Y H:i') }}
                            <br>
                            <small class="text-muted">{{ $session->last_active->diffForHumans() }}</small>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-info">{{ $session->duration }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-user-clock fa-3x mb-3 d-block"></i>
                            <p class="mb-0">Tidak ada session aktif</p>
                            <small>Belum ada pengguna yang login</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#dataTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        "pageLength": 10,
        "ordering": true,
        "searching": true,
        "order": [[6, 'desc']], // Sort by last active
        "responsive": true
    });

    // Auto refresh every 30 seconds
    setInterval(function() {
        $('#dataTable').DataTable().ajax.reload(null, false);
    }, 30000);
});
</script>
@endpush
