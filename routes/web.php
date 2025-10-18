<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\UnitController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('user', UserController::class); 
    Route::get('/peminjaman/laporan', [PeminjamanController::class, 'laporan'])->name('peminjaman.laporan');
    Route::resource('peminjaman', PeminjamanController::class);
    Route::patch('/peminjaman/{peminjaman}/kembalikan', [PeminjamanController::class, 'kembalikan'])
    ->name('peminjaman.kembalikan');
    Route::resource('kategori', KategoriController::class);
    Route::resource('lokasi', LokasiController::class);
    Route::get('/barang/laporan', [BarangController::class, 'cetakLaporan'])->name('barang.laporan');
    Route::get('/barang/search', [BarangController::class, 'search'])->name('barang.search');
    Route::resource('barang', BarangController::class);
    Route::post('/barang/{id}/konfirmasi-perawatan', [BarangController::class, 'konfirmasiPerawatan'])
    ->name('barang.konfirmasiPerawatan');
    Route::resource('units', UnitController::class)->only(['edit', 'update']);
    Route::put('/barang/{barang}/update-frekuensi-kondisi', [BarangController::class, 'updateFrekuensiKondisi'])
    ->name('barang.updateFrekuensiKondisi');
    Route::get('/barang/{barang}/units', [BarangController::class, 'units'])->name('barang.units');

});

require __DIR__.'/auth.php';
