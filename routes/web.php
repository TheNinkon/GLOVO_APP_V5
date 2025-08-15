<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\RiderLoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Rider\DashboardController as RiderDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí definimos todas las rutas para la aplicación RMS.
| La lógica está separada por roles (Admin y Rider) para mayor claridad.
|
*/

// --- RUTA PRINCIPAL (LOGIN DE RIDER) ---
Route::get('/', [RiderLoginController::class, 'showLoginForm'])->middleware('guest:rider')->name('rider.login.form');
Route::post('/', [RiderLoginController::class, 'login'])->middleware('guest:rider')->name('rider.login');
Route::post('/rider/logout', [RiderLoginController::class, 'logout'])->name('rider.logout');


// --- RUTAS DE ADMINISTRADOR ---
Route::prefix('admin')->name('admin.')->group(function () {
    // Rutas de autenticación para el admin
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->middleware('guest:web')->name('login.form');
    Route::post('/login', [AdminLoginController::class, 'login'])->middleware('guest:web')->name('login');
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

    // Rutas protegidas para el panel de admin
    Route::middleware(['auth:web'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    });
});


// --- RUTAS DE RIDER (YA AUTENTICADO) ---
Route::prefix('rider')->name('rider.')->middleware(['auth:rider'])->group(function () {
    Route::get('/dashboard', [RiderDashboardController::class, 'index'])->name('dashboard');
});
