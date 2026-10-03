<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Canalizacion extends Model
{
    use HasFactory;

    protected $table = 'canalizaciones';

    protected $fillable = [
        'alumno_id',
        'atencion_id',
        'motivo',
        'fecha',
        'hora',
        'elaborado_por',
        'firma_enfermera',
        'firma_alumno',
        'pdf_path',
        'jefatura_carrera',
        'enviado_a_jefatura',
        'fecha_envio_jefatura',
        'estatus',
    ];

    protected $casts = [
        'fecha' => 'date',
        'enviado_a_jefatura' => 'boolean',
        'fecha_envio_jefatura' => 'datetime',
    ];

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }

    public function atencion(): BelongsTo
    {
        return $this->belongsTo(Atencion::class, 'atencion_id');
    }

    public function elaborador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'elaborado_por');
    }
}
