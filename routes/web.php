<?php

use Illuminate\Support\Facades\Route;

// --- Controladores de Auth ---
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\RiderLoginController;

// --- Dashboards ---
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Rider\DashboardController as RiderDashboardController;

// --- Admin Resources ---
use App\Http\Controllers\Admin\RiderController as AdminRiderController;
use App\Http\Controllers\Admin\ForecastController;
use App\Http\Controllers\Admin\AccountController as AdminAccountController;
use App\Http\Controllers\Admin\AssignmentController as AdminAssignmentController;

// --- Rider (schedule) ---
use App\Http\Controllers\Rider\ScheduleController as RiderScheduleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí definimos todas las rutas para la aplicación RMS.
| La lógica está separada por roles (Admin y Rider) para mayor claridad.
|
| Convenciones clave:
| - Guard 'web'   => Admin/Manager
| - Guard 'rider' => Rider/Employee
| - Prefijo /admin para panel de admin
| - Prefijo /rider para vistas del rider
|
*/

/* ============================================================
|  RUTA PRINCIPAL -> Login de Rider (pública para riders)
|  Se usa 'guest:rider' para evitar que riders logueados vuelvan al login.
============================================================ */
Route::get('/', [RiderLoginController::class, 'showLoginForm'])
  ->middleware('guest:rider')
  ->name('rider.login.form');

Route::post('/', [RiderLoginController::class, 'login'])
  ->middleware('guest:rider')
  ->name('rider.login');

/* ============================================================
|  RUTAS DE ADMINISTRADOR (/admin/*)
|  - Login/Logout con guard 'web'
|  - Rutas protegidas con middleware auth:web
============================================================ */
Route::prefix('admin')->name('admin.')->group(function () {

    // --- Auth Admin (solo invitados al login) ---
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])
      ->middleware('guest:web')
      ->name('login.form');

    Route::post('/login', [AdminLoginController::class, 'login'])
      ->middleware('guest:web')
      ->name('login');

    Route::post('/logout', [AdminLoginController::class, 'logout'])
      ->name('logout');

    // --- Rutas protegidas del panel admin ---
    Route::middleware(['auth:web'])->group(function () {
        // Dashboard admin
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
          ->name('dashboard');

        // CRUD de empleados (riders)
        // Genera: index, create, store, show, edit, update, destroy
        Route::resource('/riders', AdminRiderController::class);

        // Forecasts (solo index/create/store para MVP)
        Route::resource('/forecasts', ForecastController::class)
          ->only(['index', 'create', 'store']);

        // Accounts (CRUD completo)
        // Nota: el AccountController NO debe exponer 'password_enc' en respuestas/vistas.
        Route::resource('/accounts', AdminAccountController::class);

        // --- NUEVAS RUTAS PARA ASIGNACIONES ---
        // Formulario para asignar una cuenta a un rider
        Route::get('/accounts/{account}/assign', [AdminAssignmentController::class, 'create'])
          ->name('assignments.create');

        // Procesa la asignación (cumple BR-2/3 en el servicio)
        Route::post('/accounts/{account}/assign', [AdminAssignmentController::class, 'store'])
          ->name('assignments.store');

        // Finaliza una asignación activa (libera la cuenta)
        Route::post('/assignments/{assignment}/end', [AdminAssignmentController::class, 'end'])
          ->name('assignments.end');
    });
});

/* ============================================================
|  RUTAS DE RIDER (/rider/*)
|  - Login/Logout con guard 'rider'
|  - Dashboard y Schedule protegidos con auth:rider
|  Importante: NO publicar /accounts aquí (AC-2).
============================================================ */
Route::prefix('rider')->name('rider.')->group(function () {
    // Auth Rider
    Route::get('/login', [RiderLoginController::class, 'showLoginForm'])
      ->name('login');

    Route::post('/login', [RiderLoginController::class, 'login']);

    Route::post('/logout', [RiderLoginController::class, 'logout'])
      ->name('logout');

    // Rutas protegidas (solo riders autenticados)
    Route::middleware(['auth:rider'])->group(function () {
        // Dashboard rider (KPI + asignación actual)
        Route::get('/dashboard', [RiderDashboardController::class, 'index'])
          ->name('dashboard');

        // Schedule (selección de horas)
        Route::get('/schedule', [RiderScheduleController::class, 'index'])
          ->name('schedule.index');

        Route::post('/schedule', [RiderScheduleController::class, 'store'])
          ->name('schedule.store');
    });
});
