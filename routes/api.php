<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\SlotPendakianController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\KontakController;
use App\Http\Controllers\KontenGaleriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\UiController;
use Illuminate\Support\Facades\Route;

// Endpoint login admin (Public)
Route::post('/login', [AuthController::class, 'login']);

// Endpoint logout admin (Protected)
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Endpoint Laporan Admin (Protected)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/laporan', [LaporanController::class, 'index']);
    Route::get('/admin/profile', [AuthController::class, 'profile']);
    Route::put('/admin/profile', [AuthController::class, 'updateProfile']);
});

// Endpoint CRUD lainnya (Public atau di bawah Auth Sanctum sesuai kebutuhan, tapi di sini kita daftarkan agar bisa langsung diakses/diuji)
Route::apiResource('pelanggan', PelangganController::class);
Route::apiResource('slot-pendakian', SlotPendakianController::class);
Route::apiResource('booking', BookingController::class);
Route::post('/pembayaran/{id}/bukti', [PembayaranController::class, 'uploadBukti']);
Route::apiResource('pembayaran', PembayaranController::class);
Route::apiResource('kontak', KontakController::class);
Route::apiResource('konten-galeri', KontenGaleriController::class);

// Endpoint Pengaturan
Route::get('/pengaturan', [PengaturanController::class, 'index']);
Route::put('/pengaturan', [PengaturanController::class, 'update']);

// Endpoint UI (Upload & List Gambar Halaman)
Route::get('/ui/images', [UiController::class, 'index']);
Route::post('/ui/upload', [UiController::class, 'upload']);


