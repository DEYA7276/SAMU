<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistroAlumnoRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Iniciar sesión en el sistema SAMU UTGZ.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $data = $this->authService->login($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Inicio de sesión exitoso.',
                'data' => $data,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Credenciales inválidas.',
                'errors' => $e->errors(),
            ], 401);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Registro de nuevo estudiante (crea usuario y perfil alumno sin matrícula).
     */
    public function registroAlumno(RegistroAlumnoRequest $request): JsonResponse
    {
        try {
            $data = $this->authService->registrarAlumno($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Cuenta de alumno registrada correctamente. Ya puedes completar tu ficha médica.',
                'data' => $data,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo completar el registro del alumno.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener datos del usuario autenticado actual.
     */
    public function me(Request $request): JsonResponse
    {
        $perfil = $this->authService->obtenerPerfil($request->user());

        return response()->json([
            'status' => 'success',
            'data' => $perfil,
        ]);
    }

    /**
     * Cerrar sesión y destruir el token de acceso.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'status' => 'success',
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }
}
