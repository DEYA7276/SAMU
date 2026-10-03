<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_alumno_se_puede_registrar_exitosamente_sin_matricula(): void
    {
        $payload = [
            'name' => 'Carlos López Gómez',
            'email' => 'carlos.lopez@utgz.edu.mx',
            'password' => 'secret123',
            'carrera' => 'Tecnologías de la Información',
            'telefono' => '7841122334',
            'sexo' => 'M',
            'edad' => 20,
        ];

        $response = $this->postJson('/api/registro-alumno', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.usuario.rol', 'alumno')
            ->assertJsonPath('data.usuario.email', 'carlos.lopez@utgz.edu.mx')
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'token',
                    'usuario' => ['id', 'name', 'email', 'rol'],
                    'alumno' => ['id', 'user_id', 'nombre_completo', 'matricula'],
                ],
            ]);

        // Verificar que la matrícula queda NULL para que enfermería la asigne después
        $this->assertDatabaseHas('alumnos', [
            'nombre_completo' => 'Carlos López Gómez',
            'matricula' => null,
            'foto_alumno' => null,
        ]);
    }

    public function test_login_con_credenciales_correctas(): void
    {
        $user = User::factory()->create([
            'email' => 'enfermera.test@utgz.edu.mx',
            'password' => Hash::make('password123'),
            'rol' => 'enfermeria',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'enfermera.test@utgz.edu.mx',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.usuario.rol', 'enfermeria')
            ->assertJsonStructure([
                'data' => ['token', 'usuario'],
            ]);
    }

    public function test_login_con_password_incorrecto_falla(): void
    {
        User::factory()->create([
            'email' => 'enfermera.test@utgz.edu.mx',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'enfermera.test@utgz.edu.mx',
            'password' => 'password_equivocado',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('status', 'error');
    }

    public function test_usuario_autenticado_puede_obtener_su_perfil(): void
    {
        $user = User::factory()->create([
            'rol' => 'alumno',
        ]);
        $alumno = Alumno::create([
            'user_id' => $user->id,
            'nombre_completo' => $user->name,
            'matricula' => null,
        ]);

        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/me');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.usuario.id', $user->id)
            ->assertJsonPath('data.alumno.id', $alumno->id);
    }

    public function test_alumno_no_puede_acceder_a_rutas_de_enfermeria(): void
    {
        $alumnoUser = User::factory()->create(['rol' => 'alumno']);
        $alumnoToken = $alumnoUser->createToken('token_alumno')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $alumnoToken)
            ->getJson('/api/enfermeria/ping');

        $response->assertStatus(403)
            ->assertJsonPath('status', 'error');
    }

    public function test_enfermera_puede_acceder_a_rutas_de_enfermeria(): void
    {
        $enfermeraUser = User::factory()->create(['rol' => 'enfermeria']);
        $enfermeraToken = $enfermeraUser->createToken('token_enfermera')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $enfermeraToken)
            ->getJson('/api/enfermeria/ping');

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Acceso autorizado al módulo de enfermería');
    }
}
