<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — SAMU UTGZ
|--------------------------------------------------------------------------
|
| Rutas RESTful para la comunicación entre la app Ionic + Angular y el backend.
|
*/

// Rutas Públicas (Sin Token)
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/registro-alumno', [AuthController::class, 'registroAlumno'])->name('api.registro-alumno');

// Rutas Protegidas (Requieren Token Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Sesión y Perfil
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    // Rutas protegidas exclusivas para Enfermería
    Route::middleware('role:enfermeria,admin')->prefix('enfermeria')->group(function () {
        Route::get('/ping', fn () => response()->json(['message' => 'Acceso autorizado al módulo de enfermería']));
    });

    // Rutas protegidas exclusivas para Alumnos
    Route::middleware('role:alumno')->prefix('alumno')->group(function () {
        Route::get('/ping', fn () => response()->json(['message' => 'Acceso autorizado al módulo de alumno']));
    });

    // Rutas protegidas exclusivas para Jefaturas de Carrera
    Route::middleware('role:jefatura,admin')->prefix('jefatura')->group(function () {
        Route::get('/ping', fn () => response()->json(['message' => 'Acceso autorizado al módulo de jefatura']));
    });
});
