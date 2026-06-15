<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\PrestasiSiswaController;
use App\Http\Controllers\LaporanAkademikController;
use App\Http\Controllers\DataMasterController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\DataSiswaController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\DataAkademikController;

// ----------------------------
// Auth / Session Controller
// ----------------------------
Route::middleware(['web'])->group(function(){
    Route::get('/login', [SessionController::class, 'index'])->name('login');
    Route::post('/login', [SessionController::class, 'login']);
    Route::get('/logout', [SessionController::class, 'logout'])->name('logout');
    // Forgot password routes
    Route::get('/forgot-password', [SessionController::class, 'showForgotPasswordForm'])->name('forgot-password');
    Route::post('/forgot-password', [SessionController::class, 'forgotPassword'])->name('password.email');

    Route::get('/reset-password/{token}', [SessionController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [SessionController::class, 'resetPassword'])->name('password.update');
});

Route::get('/', function () {
    return redirect()->route('login');
});

// === Administrator ===
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('DashboardAdmin');
    Route::get('/admin/sessions', [SessionController::class, 'aktifSessions'])->name('admin.session');

    // Prestasi Siswa
    Route::get('/prestasi', [PrestasiSiswaController::class, 'index'])->name('prestasi.index');
    Route::get('/get-kelas', [PrestasiSiswaController::class, 'getKelasByTahun'])->name('prestasi.get-kelas');
    Route::post('/proses-ranking', [PrestasiSiswaController::class, 'prosesRankingKelas'])->name('prestasi.proses-ranking');
    Route::post('/proses-juara', [PrestasiSiswaController::class, 'prosesJuaraUmum'])->name('prestasi.proses-juara');
    Route::get('/hasil', [PrestasiSiswaController::class, 'hasilRanking'])->name('prestasi.hasil');
    Route::get('/export-pdf', [PrestasiSiswaController::class, 'exportPdf'])->name('prestasi.export-pdf');
    Route::get('/prestasi/check-juara-readiness', [PrestasiSiswaController::class, 'checkJuaraReadiness'])->name('prestasi.check-juara-readiness');

    // Profile Administrator
    Route::get('/profile', [AdminController::class, 'profile'])->name('ProfileAdmin');
    Route::post('/profile', [AdminController::class, 'update'])->name('admin.profile.update');
    Route::get('/ubahpassword', [AdminController::class, 'ubahPassword'])->name('Admin.UbahPassword');
    Route::post('/ubahpassword', [AdminController::class, 'updatePassword'])->name('update.password');

    // Seaarch Navbar
    Route::get('/search', [SearchController::class, 'adminIndex'])->name('searchAdmin');

    // Data Akademik Kelas
    Route::get('/kelas', [DataAkademikController::class, 'dataKelas'])->name('DataKelas');
    Route::post('/kelas/store', [DataAkademikController::class, 'storeKelas'])->name('kelas.store');
    Route::put('/kelas/update/{id}', [DataAkademikController::class, 'updateKelas'])->name('kelas.update');
    Route::delete('/kelas/delete/{id}', [DataAkademikController::class, 'deleteKelas'])->name('kelas.delete');
    Route::get('kelas/export/{format}', [DataAkademikController::class, 'export'])->name('kelas.export');
    Route::get('/kelas/{id}/quickView', [DataAkademikController::class, 'quickView'])->name('kelas.quickView');

    // Data Akademik Mata Pelajaran
    Route::get('/mapel', [DataAkademikController::class, 'dataMapel'])->name('DataMapel');
    Route::post('/mapel/store', [DataAkademikController::class, 'storeMapel'])->name('mapel.store');
    Route::put('/mapel/update/{id}', [DataAkademikController::class, 'updateMapel'])->name('mapel.update');
    Route::delete('/mapel/delete/{id}', [DataAkademikController::class, 'deleteMapel'])->name('mapel.delete');
    Route::get('mapel/export', [DataAkademikController::class, 'exportMapel'])->name('mapel.export');

    // Data Akademik Jurusan
    Route::get('/jurusan', [DataAkademikController::class, 'dataJurusan'])->name('DataJurusan');
    Route::post('/jurusan/store', [DataAkademikController::class, 'storeJurusan'])->name('jurusan.store');
    Route::get('/jurusan/edit/{id}', [DataAkademikController::class, 'editJurusan'])->name('jurusan.edit');
    Route::put('/jurusan/update/{id}', [DataAkademikController::class, 'updateJurusan'])->name('jurusan.update');
    Route::delete('/jurusan/delete/{id}', [DataAkademikController::class, 'deleteJurusan'])->name('jurusan.delete');
    Route::get('/jurusan/export', [DataAkademikController::class, 'exportJurusan'])->name('jurusan.export');

     // === CRUD Jadwal ===
    Route::get('/kelolajadwal', [JadwalController::class, 'index'])->name('KelolaJadwal');
    Route::post('/admin/jadwal-kelas', [JadwalController::class, 'store'])->name('jadwal.store');
    Route::delete('/admin/jadwal/{id}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');
    Route::put('/admin/jadwal-kelas/{id}', [JadwalController::class, 'update'])->name('jadwal.update');
    Route::get('jadwal-kelas/export', [JadwalController::class, 'export'])->name('jadwal.export');

    // Laporan Akademik Siswa
    Route::get('/laporan-akademik', [LaporanAkademikController::class, 'adminIndex'])->name('LaporanAkademikAdmin');
    Route::post('/laporan-akademik/store', [LaporanAkademikController::class, 'adminStore'])->name('laporan.store');
    Route::get('/laporan-akademik/admin/unduh', [LaporanAkademikController::class, 'unduhAdmin'])->name('laporan.unduh');

    // Monitoring Kehadiran
    Route::get('/kehadiran', [MonitoringController::class, 'monitoringKehadiran'])->name('MonitoringKehadiran');
    Route::get('/kehadiran/detail/{siswa}', [MonitoringController::class, 'detailKehadiran'])->name('detail-kehadiran');

    // CRUD Kehadiran
    Route::post('/kehadiran/store', [MonitoringController::class, 'storeKehadiran'])->name('kehadiran.store');
    Route::put('/kehadiran/{id}', [MonitoringController::class, 'updateKehadiran'])->name('kehadiran.update');
    Route::delete('/kehadiran/{id}', [MonitoringController::class, 'deleteKehadiran'])->name('kehadiran.delete');

    // Input Kehadiran Batch (Multiple Siswa)
    Route::get('/kehadiran/input-batch', [MonitoringController::class, 'inputKehadiranBatch'])->name('input-kehadiran-batch');
    Route::post('/kehadiran/store-batch', [MonitoringController::class, 'storeKehadiranBatch'])->name('kehadiran.store-batch');

    // Monitoring Nilai
    Route::get('/nilai', [MonitoringController::class, 'monitoringNilai'])->name('MonitoringNilai');
    Route::get('/nilai/detail/{siswa}', [MonitoringController::class, 'detailNilai'])->name('detail-nilai');

    // CRUD Nilai
    Route::post('/monitoring/nilai/store', [MonitoringController::class, 'storeNilai'])->name('nilai.store');
    Route::put('/monitoring/nilai/{id}', [MonitoringController::class, 'updateNilai'])->name('nilai.update');
    Route::delete('/monitoring/nilai/{id}', [MonitoringController::class, 'deleteNilai'])->name('nilai.delete');

    // Halaman utama data master admin
    Route::get('/datamasteradmin', [DataMasterController::class, 'admin'])->name('dataMaster.admin');

    // CRUD ADMIN
    Route::post('/data-master/admin/store', [DataMasterController::class, 'storeAdmin'])->name('dataMaster.admin.store');
    Route::put('/data-master/admin/update/{id}', [DataMasterController::class, 'updateAdmin'])->name('dataMaster.admin.update');
    Route::delete('/data-master/admin/delete/{id}', [DataMasterController::class, 'deleteAdmin'])->name('dataMaster.admin.delete');

    // Halaman utama data master guru
    Route::get('/datamasterguru', [DataMasterController::class, 'guru'])->name('dataMaster.guru');
    Route::get('/data-master/guru/filter', [DataMasterController::class, 'getGuruFiltered'])->name('dataMaster.guru.filter');
    Route::get('/data-master/guru/bidang-keahlian', [DataMasterController::class, 'getBidangKeahlianList'])->name('dataMaster.guru.bidangKeahlian');

    // CRUD GURU
    Route::post('/data-master/guru/store', [DataMasterController::class, 'storeGuru'])->name('dataMaster.guru.store');
    Route::put('/data-master/guru/update/{id}', [DataMasterController::class, 'updateGuru'])->name('dataMaster.guru.update');
    Route::delete('/data-master/guru/delete/{id}', [DataMasterController::class, 'deleteGuru'])->name('dataMaster.guru.delete');

    // Halaman utama data master siswa
    Route::get('/datamastersiswa', [DataMasterController::class, 'siswa'])->name('dataMaster.siswa');

    // CRUD SISWA
    Route::post('/data-master/siswa/store', [DataMasterController::class, 'storeSiswa'])->name('dataMaster.siswa.store');
    Route::put('/data-master/siswa/update/{id}', [DataMasterController::class, 'updateSiswa'])->name('dataMaster.siswa.update');
    Route::delete('/data-master/siswa/delete/{id}', [DataMasterController::class, 'deleteSiswa'])->name('dataMaster.siswa.delete');
});

// Route Role GURU
Route::prefix('guru')->middleware(['auth'])->group(function () {
    Route::get('/', [GuruController::class, 'index'])->name('DashboardGuru');
    Route::get('/filter-data', [GuruController::class, 'filterData'])->name('filterData');
    Route::get('/filter-data-kelas', [GuruController::class, 'filterDataKelas'])->name('filterDataKelas');
    Route::get('/datasiswa', [DataSiswaController::class, 'index'])->name('DataSiswa');

    // Kelola Data Nilai dan Kehadiran Siswa
    // Route Kelola Kehadiran
    Route::get('/kelola/kehadiran', [DataSiswaController::class, 'kelolaKehadiran'])->name('DataKehadiran');
    Route::post('/kehadiran/simpan', [DataSiswaController::class, 'storeKehadiranHarian'])->name('storeKehadiranHarian');

    // Route Kelola Nilai
    Route::get('/kelola/nilai', [DataSiswaController::class, 'kelolaNilai'])->name('DataNilai');
    Route::post('/nilai/simpan', [DataSiswaController::class, 'storeOrUpdateNilai'])->name('storeNilai');


    // Laporan Akademik Guru
    Route::get('/laporan-akademik', [LaporanAkademikController::class, 'guruIndex'])->name('LaporanAkademikGuru');
    Route::get('/laporan-akademik/detail/{siswa}', [LaporanAkademikController::class, 'detailSiswa'])->name('laporan.akademik.detail');
    Route::get('/laporan/unduh-semua', [LaporanAkademikController::class, 'unduhGuru'])->name('unduhGuru');
    Route::get('/kelas-by-tahun-ajaran', [LaporanAkademikController::class, 'getKelasByTahunAjaran'])
    ->name('kelas.by.tahun.ajaran');
    Route::get('/laporan/get-kelas', [LaporanAkademikController::class, 'getKelasByTahunAjaran'])->name('laporan.get-kelas');

    // Profile Guru
    Route::get('/profile', [GuruController::class, 'profile'])->name('ProfileGuru');
    Route::post('/profile', [GuruController::class, 'update'])->name('guru.profile.update');
    Route::get('/ubahpassword', [GuruController::class, 'ubahPassword'])->name('Guru.UbahPassword');
    Route::post('/ubahpassword', [GuruController::class, 'updatePassword'])->name('update.password');

    // Seaarch Navbar
    Route::get('/search', [SearchController::class, 'guruIndex'])->name('searchGuru');
});

// Route Role SISWA
Route::prefix('siswa')->middleware(['auth'])->group(function () {
    Route::get('/', [SiswaController::class, 'index'])->name('DashboardSiswa');

    // Data Akademik Siswa
    Route::get('/lihatdata/nilai', [SiswaController::class, 'lihatNilai'])->name('Nilai');
    Route::get('/lihatdata/kehadiran', [SiswaController::class, 'lihatKehadiran'])->name('Kehadiran');
    Route::get('/lihatdata/jadwal-pelajaran', [SiswaController::class, 'lihatJadwal'])->name('JadwalPelajaran');

    // Laporan Akademik Siswa
    Route::get('/laporansiswa', [LaporanAkademikController::class, 'siswaIndex'])->name('LaporanAkademikSiswa');
    Route::get('siswa/pdf/{nis}/{semester?}', [LaporanAkademikController::class, 'unduh'])->name('laporanSiswa.pdf');

    // Profile Siswa
    Route::get('/profile', [SiswaController::class, 'profile'])->name('ProfileSiswa');
    Route::post('/profile', [SiswaController::class, 'update'])->name('siswa.profile.update');
    Route::get('/ubahpassword', [SiswaController::class, 'ubahPassword'])->name('Siswa.UbahPassword');
    Route::post('/ubahpassword', [SiswaController::class, 'updatePassword'])->name('update.password');

    // Seaarch Navbar
    Route::get('/search', [SearchController::class, 'siswaIndex'])->name('searchSiswa');
});
