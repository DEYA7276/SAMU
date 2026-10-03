<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FichaMedicaController;
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

// Captura de Ficha Médica por Alumno Nuevo (Sin correo ni carrera ni contraseña obligatorios)
Route::post('/fichas-medicas', [FichaMedicaController::class, 'storePublic'])->name('api.fichas.storePublic');
Route::get('/fichas-medicas/{id}', [FichaMedicaController::class, 'show'])->name('api.fichas.show');

// Rutas Protegidas (Requieren Token Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Sesión y Perfil
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    // Rutas protegidas exclusivas para Enfermería
    Route::middleware('role:enfermeria,admin')->prefix('enfermeria')->group(function () {
        Route::get('/ping', fn () => response()->json(['message' => 'Acceso autorizado al módulo de enfermería']));
        Route::get('/fichas-medicas', [FichaMedicaController::class, 'index'])->name('api.enfermeria.fichas.index');
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
