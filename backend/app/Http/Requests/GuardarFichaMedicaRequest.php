<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GuardarFichaMedicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Identificación inicial del alumno (sin matrícula, sin foto, sin correo obligatorio, sin carrera)
            'nombre_completo' => ['required', 'string', 'max:255', 'min:3'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'contacto_emergencia_nombre' => ['nullable', 'string', 'max:255'],
            'contacto_emergencia_telefono' => ['nullable', 'string', 'max:20'],

            // Antecedentes (arrays o strings)
            'antecedentes_familiares' => ['nullable', 'array'],
            'antecedentes_personales' => ['nullable', 'array'],

            // Antecedentes médicos
            'cirugias' => ['nullable', 'string'],
            'traumatismos' => ['nullable', 'string'],
            'alergias' => ['nullable', 'string'],

            // Situación médica actual
            'padecimientos' => ['nullable', 'string'],
            'tratamiento_medico' => ['nullable', 'string'],
            'control_preventivo' => ['nullable', 'string'],
            'medicamentos_restringidos' => ['nullable', 'string'],

            // Médico particular
            'nombre_medico' => ['nullable', 'string', 'max:255'],
            'telefono_medico' => ['nullable', 'string', 'max:20'],
            'hospital_preferencia' => ['nullable', 'string', 'max:255'],

            // Autorización del tutor
            'nombre_tutor' => ['required', 'string', 'max:255'],
            'firma_tutor' => ['nullable', 'string'], // Imagen Base64
            'autorizacion_imss' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_completo.required' => 'El nombre completo del alumno es obligatorio.',
            'nombre_tutor.required' => 'El nombre del padre o tutor es obligatorio.',
            'autorizacion_imss.required' => 'Debes indicar la autorización para atención y traslado médico.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Errores de validación en la captura de la ficha médica.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
