<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ApiLogController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\GraphController;
use App\Http\Controllers\SystemStatusController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// API endpoint terbuka untuk dibaca oleh Node-RED
Route::get('/api/settings/{device_id?}', [DeviceController::class, 'getApiSettings']);
Route::post('/api/log/event', [ApiLogController::class, 'storeLog']);

// Rute yang membutuhkan Login
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Halaman 
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
    Route::get('/grafik', [GraphController::class, 'index'])->name('grafik.index');
    Route::get('/api/grafik/data', [GraphController::class, 'getData'])->name('api.grafik.data');
    Route::get('/status', [SystemStatusController::class, 'index'])->name('status.index');
    Route::get('/pengaturan', [SettingController::class, 'index'])->name('pengaturan.index');
    
    // Aksi Form & Tombol Perangkat
    Route::post('/device/settings/update', [DeviceController::class, 'updateSettings'])->name('device.settings.update');
    Route::post('/device/relay/control', [DeviceController::class, 'controlRelay'])->name('device.relay.control');

    // Rute Tindakan Darurat / Maintenance
    Route::post('/device/maintenance/reset-cycle', [DeviceController::class, 'resetCycle'])->name('device.maintenance.reset');
    Route::post('/device/maintenance/restart', [DeviceController::class, 'restartDevice'])->name('device.maintenance.restart');
    
    // ==========================================
    // RUTE PROFIL BAWAAN LARAVEL BREEZE
    // ==========================================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';