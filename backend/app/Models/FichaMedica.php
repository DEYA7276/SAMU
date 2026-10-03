<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FichaMedica extends Model
{
    use HasFactory;

    protected $table = 'fichas_medicas';

    protected $fillable = [
        'alumno_id',
        'nombre_completo',
        'antecedentes_familiares',
        'antecedentes_personales',
        'cirugias',
        'traumatismos',
        'alergias',
        'padecimientos',
        'tratamiento_medico',
        'control_preventivo',
        'medicamentos_restringidos',
        'nombre_medico',
        'telefono_medico',
        'hospital_preferencia',
        'nombre_tutor',
        'firma_tutor',
        'autorizacion_imss',
        'estatus_validacion',
        'observaciones_enfermeria',
        'validado_por',
        'fecha_validacion',
    ];

    protected $casts = [
        'antecedentes_familiares' => 'array',
        'antecedentes_personales' => 'array',
        'autorizacion_imss' => 'boolean',
        'fecha_validacion' => 'datetime',
    ];

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }

    public function validador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validado_por');
    }
}
