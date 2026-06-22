@extends('Layouts.LayoutsAdmin.app')

@section('title')
<title> Sistem Akademik - Data Master Guru </title>
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-chalkboard-teacher"></i> Data Master Guru
    </h1>
    <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#addGuruModal">
        <i class="fas fa-plus fa-sm"></i> Tambah Guru
    </button>
</div>

<!-- DataTable Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table"></i> Daftar Guru
        </h6>
        <div>
            <span class="badge badge-primary badge-pill mr-2">{{ $guru->total() }} Guru</span>
        </div>
    </div>

    <!-- FILTER SECTION - LENGKAP DENGAN RESET -->
    <div class="card-header py-2 bg-light">
        <form method="GET" action="{{ url()->current() }}" id="filterForm">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-primary">
                            <i class="fas fa-filter"></i> Filter Bidang Keahlian
                        </label>
                        <select class="form-control form-control-sm" name="bidang_keahlian" id="filterBidang" onchange="this.form.submit()">
                            <option value="">Semua Bidang Keahlian</option>
                            @php
                                // Mengumpulkan semua bidang keahlian unik dari data guru
                                $bidangList = [];
                                foreach($guru as $g) {
                                    if($g->guruProfile && $g->guruProfile->bidang_keahlian) {
                                        $bidangList[] = $g->guruProfile->bidang_keahlian;
                                    }
                                }
                                $bidangList = array_unique($bidangList);
                                sort($bidangList);
                            @endphp
                            @foreach($bidangList as $bidang)
                                <option value="{{ $bidang }}" {{ request('bidang_keahlian') == $bidang ? 'selected' : '' }}>{{ $bidang }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-primary">
                            <i class="fas fa-search"></i> Pencarian Cepat
                        </label>
                        <input type="text" class="form-control form-control-sm" name="search" id="searchInput" placeholder="Cari nama, email, NIP..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4 text-right">
                    <div class="form-group mb-0" style="padding-top: 28px;">
                        <button type="button" class="btn btn-sm btn-secondary" onclick="resetGuruFilters()">
                            <i class="fas fa-undo"></i> Reset Filter
                        </button>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                </div>
            </div>
        </form>
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

        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        <!-- Info Filter Aktif -->
        <div class="filter-info mb-3" id="filterInfo" style="display: none;">
            <div class="alert alert-info py-2 mb-0">
                <i class="fas fa-info-circle"></i>
                <span id="filterText">Filter aktif: </span>
                <button class="btn btn-sm btn-link text-danger" onclick="resetGuruFilters()">
                    <i class="fas fa-times"></i> Hapus filter
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="guruTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr class="text-center">
                        <th width="5%">No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>NIP</th>
                        <th>Bidang Keahlian</th>
                        <th>No HP</th>
                        <th>Status</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody id="guruTableBody">
                    @forelse ($guru as $key => $row)
                    <tr class="guru-row">
                        <td class="text-center guru-no">{{ $key + 1 }}</td>
                        <td class="guru-nama">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle mr-2">
                                    <i class="fas fa-user-circle text-warning fa-2x"></i>
                                </div>
                                <div>
                                    <strong>{{ $row->name }}</strong>
                                </div>
                            </div>
                        </td>
                        <td class="guru-email">
                            <a href="mailto:{{ $row->email }}" class="text-decoration-none">
                                <i class="fas fa-envelope text-info"></i> {{ $row->email }}
                            </a>
                        </td>
                        <td class="text-center guru-nip">
                            @if($row->guruProfile && $row->guruProfile->nip)
                                <span class="badge badge-primary">{{ $row->guruProfile->nip }}</span>
                            @else
                                <span class="badge badge-light">-</span>
                            @endif
                        </td>
                        <td class="guru-bidang">
                            @if($row->guruProfile && $row->guruProfile->bidang_keahlian)
                                <span class="badge badge-success bidang-text">{{ $row->guruProfile->bidang_keahlian }}</span>
                            @else
                                <span class="text-muted bidang-text">-</span>
                            @endif
                        </td>
                        <td class="text-center guru-nohp">
                            @if($row->guruProfile && $row->guruProfile->no_hp)
                                <a href="tel:{{ $row->guruProfile->no_hp }}" class="text-decoration-none">
                                    <i class="fas fa-phone text-success"></i> {{ $row->guruProfile->no_hp }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center guru-status">
                            @php
                                // Cek apakah guru memiliki profil dan jadwal mengajar
                                $hasJadwal = $row->guruProfile && $row->guruProfile->jadwalKelas->isNotEmpty();
                                $statusGuru = $hasJadwal ? 'Aktif' : 'Tidak Aktif';
                                $badgeClass = $hasJadwal ? 'badge-success' : 'badge-secondary';
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                <i class="fas fa-circle fa-xs"></i> {{ $statusGuru }}
                            </span>
                        </td>
                        <td class="text-center guru-aksi">
                            <div class="d-flex justify-content-center align-items-center" style="gap: 5px;">
                                <!-- Edit Button -->
                                <button class="btn btn-warning btn-sm btn-circle"
                                        data-toggle="modal"
                                        data-target="#editGuruModal{{ $row->id }}"
                                        title="Edit Data">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <!-- Delete Button -->
                                <form action="{{ route('dataMaster.guru.delete', $row->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirmDeleteGuru()">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm btn-circle"
                                            type="submit"
                                            title="Hapus Data">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Edit (sudah lengkap) -->
                    <div class="modal fade" id="editGuruModal{{ $row->id }}" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <form action="{{ route('dataMaster.guru.update', $row->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title">
                                            <i class="fas fa-edit"></i> Edit Data Guru
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-info alert-dismissible fade show">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Info:</strong> Kosongkan field password jika tidak ingin mengubah password.
                                            <button type="button" class="close" data-dismiss="alert">
                                                <span>&times;</span>
                                            </button>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-bold">
                                                    Nama Lengkap <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="name" value="{{ $row->name }}" class="form-control" required>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-bold">
                                                    Email <span class="text-danger">*</span>
                                                </label>
                                                <input type="email" name="email" value="{{ $row->email }}" class="form-control" required>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-bold">Password Baru</label>
                                                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah" minlength="8">
                                                <small class="form-text text-muted">Minimal 8 karakter</small>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-bold">NIP</label>
                                                <input type="text" name="nip" value="{{ $row->guruProfile->nip ?? '' }}" class="form-control">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-bold">Bidang Keahlian</label>
                                                <input type="text" name="bidang_keahlian" value="{{ $row->guruProfile->bidang_keahlian ?? '' }}" class="form-control">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-bold">No HP</label>
                                                <input type="text" name="no_hp" value="{{ $row->guruProfile->no_hp ?? '' }}" class="form-control" placeholder="08xxxxxxxxxx">
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <label class="font-weight-bold">Alamat</label>
                                                <textarea name="alamat" class="form-control" rows="3">{{ $row->guruProfile->alamat ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            <i class="fas fa-times"></i> Batal
                                        </button>
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-save"></i> Simpan Perubahan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-chalkboard-teacher fa-3x mb-3 d-block"></i>
                            <p class="mb-0">Belum ada data guru</p>
                            <small>Silakan tambah guru baru dengan tombol di atas</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-3 d-flex justify-content-between align-items-center">
            <div class="text-muted">
                <span class="text-muted">Menampilkan {{ $guru->firstItem() }} - {{ $guru->lastItem() }} dari {{ $guru->total() }} Guru</span>
            </div>
            <div class="d-flex justify-content-end">
                {{ $guru->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah (sudah lengkap) -->
<div class="modal fade" id="addGuruModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('dataMaster.guru.store') }}" method="POST">
                @csrf

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-chalkboard-teacher"></i> Tambah Data Guru
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Informasi:</strong>
                        Silakan lengkapi data guru dengan benar. Field bertanda
                        <span class="text-danger">*</span> wajib diisi.
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" class="form-control"
                                placeholder="Masukkan nama lengkap guru" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" class="form-control"
                                placeholder="guru@example.com" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">
                                Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" name="password" class="form-control"
                                placeholder="Minimal 8 karakter" required minlength="8">
                            <div class="password-strength mt-1">
                                <small class="text-muted">Kekuatan password: <span id="passwordStrength">Lemah</span></small>
                                <div class="progress" style="height: 5px;">
                                    <div id="passwordStrengthBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">NIP</label>
                            <input type="text" name="nip" class="form-control"
                                placeholder="Nomor Induk Pegawai">
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Bidang Keahlian</label>
                            <input type="text" name="bidang_keahlian" class="form-control"
                                placeholder="Produktif TKJ">
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">No HP</label>
                            <input type="text" name="no_hp" class="form-control"
                                placeholder="08xxxxxxxxxx">
                        </div>

                        <div class="col-md-12 form-group">
                            <label class="font-weight-bold">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3"
                                placeholder="Alamat lengkap guru"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .password-strength .progress {
        background-color: #e9ecef;
    }
    .password-strength .progress-bar {
        transition: width 0.3s ease;
    }
    .filter-highlight {
        background-color: #fff3cd !important;
        transition: background-color 0.5s ease;
    }
    .filter-info {
        transition: all 0.3s ease;
    }
    .badge-filter {
        cursor: pointer;
    }
    .badge-filter:hover {
        opacity: 0.8;
    }
    /* Style untuk dropdown filter */
    #filterBidang, #searchInput {
        border: 1px solid #d1d3e2;
    }
    #filterBidang:focus, #searchInput:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
</style>
@endpush

@push('scripts')
<script>
// Fungsi filter untuk guru
document.addEventListener('DOMContentLoaded', function() {

    // Ambil elemen-elemen yang diperlukan
    const filterBidang = document.getElementById('filterBidang');
    const searchInput = document.getElementById('searchInput');
    const rows = document.querySelectorAll('.guru-row');
    const filterInfo = document.getElementById('filterInfo');
    const filterText = document.getElementById('filterText');
    const totalRowsSpan = document.getElementById('totalRows');
    const filteredInfo = document.getElementById('filteredInfo');

    // Fungsi untuk melakukan filter
    function filterGuruTable() {
        const bidangValue = filterBidang ? filterBidang.value.toLowerCase().trim() : '';
        const searchValue = searchInput ? searchInput.value.toLowerCase().trim() : '';

        let visibleCount = 0;
        let filterActive = false;

        rows.forEach(row => {
            // Ambil data dari row dengan lebih aman
            const nama = row.querySelector('.guru-nama')?.textContent.toLowerCase() || '';
            const email = row.querySelector('.guru-email')?.textContent.toLowerCase() || '';
            const nip = row.querySelector('.guru-nip')?.textContent.toLowerCase() || '';
            const bidang = row.querySelector('.bidang-text')?.textContent.toLowerCase() || '';

            // Cek apakah memenuhi filter bidang
            let matchBidang = true;
            if (bidangValue !== '') {
                matchBidang = bidang.includes(bidangValue);
                if (matchBidang) filterActive = true;
            }

            // Cek apakah memenuhi pencarian
            let matchSearch = true;
            if (searchValue !== '') {
                matchSearch = nama.includes(searchValue) ||
                             email.includes(searchValue) ||
                             nip.includes(searchValue);
                if (matchSearch) filterActive = true;
            }

            // Tampilkan atau sembunyikan row
            if (matchBidang && matchSearch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update info filter
        updateFilterInfo(filterActive, bidangValue, searchValue, visibleCount);

        // Tampilkan pesan jika tidak ada data
        const tbody = document.getElementById('guruTableBody');
        let noDataRow = document.getElementById('noDataRow');

        if (visibleCount === 0 && rows.length > 0) {
            if (!noDataRow) {
                noDataRow = document.createElement('tr');
                noDataRow.id = 'noDataRow';
                noDataRow.innerHTML = '<td colspan="8" class="text-center text-muted py-4"><i class="fas fa-search fa-3x mb-3 d-block"></i><p class="mb-0">Tidak ada guru yang sesuai dengan filter</p><small>Silakan atur ulang filter</small></td>';
                tbody.appendChild(noDataRow);
            }
        } else {
            if (noDataRow) {
                noDataRow.remove();
            }
        }

        // Update jumlah data
        if (totalRowsSpan) {
            totalRowsSpan.textContent = rows.length;
        }
    }

    // Fungsi untuk update info filter
    function updateFilterInfo(isActive, bidang, search, visibleCount) {
        if (!filterInfo || !filterText || !filteredInfo) return;

        if (isActive) {
            filterInfo.style.display = 'block';
            let filterDesc = [];
            if (bidang) filterDesc.push(`Bidang Keahlian: <strong>${filterBidang.value}</strong>`);
            if (search) filterDesc.push(`Pencarian: <strong>"${searchInput.value}"</strong>`);
            filterText.innerHTML = 'Filter aktif: ' + filterDesc.join(', ');

            if (filteredInfo) {
                filteredInfo.innerHTML = `Menampilkan <strong>${visibleCount}</strong> dari <strong>${rows.length}</strong> data`;
            }
        } else {
            filterInfo.style.display = 'none';
            if (filteredInfo) {
                filteredInfo.innerHTML = '';
            }
        }
    }

    window.resetGuruFilters = function() {
    // Reset nilai input filter
    document.getElementById('filterBidang').value = '';
    document.getElementById('searchInput').value = '';

    // Submit form untuk memuat ulang data tanpa filter
    document.getElementById('filterForm').submit();
    };

    // Event listeners
    if (filterBidang) filterBidang.addEventListener('change', filterGuruTable);
    if (searchInput) searchInput.addEventListener('keyup', filterGuruTable);

    // Auto-hide alerts
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(alert => {
            if (alert.classList.contains('alert-dismissible')) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            }
        });
    }, 5000);
});

// Fungsi konfirmasi hapus
function confirmDeleteGuru() {
    return confirm('Apakah Anda yakin ingin menghapus guru ini?');
}
</script>
@endpush
