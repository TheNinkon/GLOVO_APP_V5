<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\RiderLoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Rider\DashboardController as RiderDashboardController;
use App\Http\Controllers\Admin\RiderController as AdminRiderController;
use App\Http\Controllers\Admin\ForecastController; // Nueva línea agregada

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

        // CRUD de empleados (riders)
        Route::resource('/riders', AdminRiderController::class);

        // Forecasts (solo index, create y store)
        Route::resource('/forecasts', ForecastController::class)->only(['index', 'create', 'store']);
    });
});

// --- RUTAS DE RIDER ---
Route::prefix('rider')->name('rider.')->group(function () {

    Route::get('/login', [RiderLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [RiderLoginController::class, 'login']);
    Route::post('/logout', [RiderLoginController::class, 'logout'])->name('logout');

    Route::middleware(['auth:rider'])->group(function () {
        Route::get('/dashboard', [RiderDashboardController::class, 'index'])->name('dashboard');

        // ---- NUEVAS RUTAS PARA EL HORARIO ----
        Route::get('/schedule', [App\Http\Controllers\Rider\ScheduleController::class, 'index'])->name('schedule.index');
        Route::post('/schedule', [App\Http\Controllers\Rider\ScheduleController::class, 'store'])->name('schedule.store');
    });
});
