<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\FichaMedica;
use Illuminate\Support\Facades\DB;

class FichaMedicaService
{
    /**
     * Registrar la ficha médica inicial capturada por el alumno desde su celular.
     * Regla estricta: NO se pide matrícula, foto, correo ni carrera en esta fase.
     */
    public function registrarFichaInicial(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // 1. Crear el registro base del Alumno
            $alumno = Alumno::create([
                'user_id' => null, // El alumno nuevo aún no tiene usuario/contraseña
                'matricula' => null, // Enfermería la asignará en el consultorio
                'foto_alumno' => null, // Enfermería la capturará en el consultorio
                'carrera' => null, // Sin carrera en fase inicial
                'correo' => null, // Sin correo en fase inicial
                'nombre_completo' => trim($data['nombre_completo']),
                'telefono' => $data['telefono'] ?? null,
                'contacto_emergencia_nombre' => $data['contacto_emergencia_nombre'] ?? null,
                'contacto_emergencia_telefono' => $data['contacto_emergencia_telefono'] ?? null,
            ]);

            // 2. Crear la Ficha Médica asociada con estatus pendiente
            $ficha = FichaMedica::create([
                'alumno_id' => $alumno->id,
                'nombre_completo' => $alumno->nombre_completo,
                'antecedentes_familiares' => $data['antecedentes_familiares'] ?? [],
                'antecedentes_personales' => $data['antecedentes_personales'] ?? [],
                'cirugias' => $data['cirugias'] ?? 'Ninguna',
                'traumatismos' => $data['traumatismos'] ?? 'Ninguno',
                'alergias' => $data['alergias'] ?? 'Ninguna',
                'padecimientos' => $data['padecimientos'] ?? 'Ninguno',
                'tratamiento_medico' => $data['tratamiento_medico'] ?? null,
                'control_preventivo' => $data['control_preventivo'] ?? null,
                'medicamentos_restringidos' => $data['medicamentos_restringidos'] ?? null,
                'nombre_medico' => $data['nombre_medico'] ?? null,
                'telefono_medico' => $data['telefono_medico'] ?? null,
                'hospital_preferencia' => $data['hospital_preferencia'] ?? null,
                'nombre_tutor' => trim($data['nombre_tutor']),
                'firma_tutor' => $data['firma_tutor'] ?? null,
                'autorizacion_imss' => (bool) ($data['autorizacion_imss'] ?? false),
                'estatus_validacion' => 'pendiente_validacion',
                'observaciones_enfermeria' => null,
                'validado_por' => null,
                'fecha_validacion' => null,
            ]);

            $folio = sprintf('SAMU-%s-%04d', date('Y'), $ficha->id);

            return [
                'folio' => $folio,
                'alumno' => $alumno,
                'ficha' => $ficha,
            ];
        });
    }

    /**
     * Consultar una ficha médica por ID con datos del alumno.
     */
    public function obtenerFicha(int $id): ?FichaMedica
    {
        return FichaMedica::with(['alumno', 'validador'])->find($id);
    }

    /**
     * Listar fichas médicas filtradas por estatus de validación.
     */
    public function listarFichas(?string $estatus = null)
    {
        $query = FichaMedica::with('alumno')->latest();

        if ($estatus) {
            $query->where('estatus_validacion', $estatus);
        }

        return $query->get();
    }
}
