<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Autenticar usuario y generar token de Sanctum.
     */
    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Generar token personal de acceso con Sanctum
        $token = $user->createToken('samu_auth_token')->plainTextToken;

        // Cargar perfil de alumno si corresponde
        $alumno = null;
        if ($user->rol === 'alumno') {
            $alumno = Alumno::with('fichaMedica')->where('user_id', $user->id)->first();
        }

        return [
            'token' => $token,
            'usuario' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rol' => $user->rol,
                'carrera' => $user->carrera,
            ],
            'alumno' => $alumno,
        ];
    }

    /**
     * Registrar un nuevo alumno con su cuenta de usuario y perfil de alumno.
     * Respeta la Regla 4: No se solicita matrícula ni foto al alumno.
     */
    public function registrarAlumno(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'rol' => 'alumno',
                'carrera' => $data['carrera'] ?? null,
            ]);

            $alumno = Alumno::create([
                'user_id' => $user->id,
                'matricula' => null, // Enfermería la asignará posteriormente
                'foto_alumno' => null, // Enfermería la subirá posteriormente
                'nombre_completo' => $data['name'],
                'sexo' => $data['sexo'] ?? null,
                'edad' => $data['edad'] ?? null,
                'carrera' => $data['carrera'] ?? null,
                'telefono' => $data['telefono'] ?? null,
                'correo' => $data['email'],
                'domicilio' => $data['domicilio'] ?? null,
                'contacto_emergencia_nombre' => $data['contacto_emergencia_nombre'] ?? null,
                'contacto_emergencia_telefono' => $data['contacto_emergencia_telefono'] ?? null,
            ]);

            $token = $user->createToken('samu_auth_token')->plainTextToken;

            return [
                'token' => $token,
                'usuario' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'rol' => $user->rol,
                    'carrera' => $user->carrera,
                ],
                'alumno' => $alumno,
            ];
        });
    }

    /**
     * Obtener el perfil completo del usuario autenticado.
     */
    public function obtenerPerfil(User $user): array
    {
        $alumno = null;
        if ($user->rol === 'alumno') {
            $alumno = Alumno::with('fichaMedica')->where('user_id', $user->id)->first();
        }

        return [
            'usuario' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rol' => $user->rol,
                'carrera' => $user->carrera,
            ],
            'alumno' => $alumno,
        ];
    }

    /**
     * Cerrar sesión y revocar el token actual.
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
