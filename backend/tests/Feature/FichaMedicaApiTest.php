<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FichaMedicaApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_alumno_nuevo_puede_guardar_ficha_medica_sin_correo_ni_carrera_ni_matricula(): void
    {
        $payload = [
            'nombre_completo' => 'Luis Fernando Morales Ruiz',
            'telefono' => '7841556677',
            'contacto_emergencia_nombre' => 'Carmen Ruiz (Madre)',
            'contacto_emergencia_telefono' => '7841889900',
            'antecedentes_familiares' => ['Diabetes', 'Hipertensión'],
            'antecedentes_personales' => ['Rinitis alérgica'],
            'cirugias' => 'Ninguna',
            'traumatismos' => 'Fractura de radio izquierdo en 2021',
            'alergias' => 'Amoxicilina',
            'padecimientos' => 'Ninguno activo',
            'tratamiento_medico' => 'Ninguno',
            'control_preventivo' => 'Revisión anual',
            'medicamentos_restringidos' => 'Amoxicilina y derivados',
            'nombre_medico' => 'Dr. Javier Santos',
            'telefono_medico' => '7841334455',
            'hospital_preferencia' => 'Hospital Integral Gutiérrez Zamora',
            'nombre_tutor' => 'Carmen Ruiz Soto',
            'firma_tutor' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUA',
            'autorizacion_imss' => true,
        ];

        $response = $this->postJson('/api/fichas-medicas', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'folio',
                    'alumno' => ['id', 'nombre_completo', 'matricula', 'foto_alumno', 'carrera', 'correo'],
                    'ficha' => ['id', 'alumno_id', 'nombre_completo', 'estatus_validacion'],
                ],
            ]);

        // Verificar que en base de datos el alumno queda sin matrícula, foto, carrera ni correo
        $this->assertDatabaseHas('alumnos', [
            'nombre_completo' => 'Luis Fernando Morales Ruiz',
            'matricula' => null,
            'foto_alumno' => null,
            'carrera' => null,
            'correo' => null,
        ]);

        // Verificar que la ficha queda en estatus pendiente_validacion
        $this->assertDatabaseHas('fichas_medicas', [
            'nombre_completo' => 'Luis Fernando Morales Ruiz',
            'estatus_validacion' => 'pendiente_validacion',
            'autorizacion_imss' => 1,
        ]);
    }

    public function test_validacion_falla_si_falta_nombre_completo_o_tutor(): void
    {
        $response = $this->postJson('/api/fichas-medicas', [
            'cirugias' => 'Ninguna',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('status', 'error')
            ->assertJsonValidationErrors(['nombre_completo', 'nombre_tutor', 'autorizacion_imss']);
    }

    public function test_enfermera_puede_listar_fichas_pendientes(): void
    {
        $enfermera = User::factory()->create(['rol' => 'enfermeria']);
        $token = $enfermera->createToken('enfermera_token')->plainTextToken;

        // Registrar una ficha primero
        $this->postJson('/api/fichas-medicas', [
            'nombre_completo' => 'Estudiante Prueba Ficha',
            'nombre_tutor' => 'Tutor Prueba',
            'autorizacion_imss' => true,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/enfermeria/fichas-medicas?estatus=pendiente_validacion');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $data = $response->json('data');
        $this->assertIsArray($data);
        $this->assertGreaterThanOrEqual(1, count($data));
    }
}
