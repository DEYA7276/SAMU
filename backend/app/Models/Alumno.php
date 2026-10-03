<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Alumno extends Model
{
    use HasFactory;

    protected $table = 'alumnos';

    protected $fillable = [
        'user_id',
        'matricula',
        'foto_alumno',
        'nombre_completo',
        'sexo',
        'edad',
        'carrera',
        'telefono',
        'correo',
        'domicilio',
        'contacto_emergencia_nombre',
        'contacto_emergencia_telefono',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fichaMedica(): HasOne
    {
        return $this->hasOne(FichaMedica::class, 'alumno_id');
    }

    public function atenciones(): HasMany
    {
        return $this->hasMany(Atencion::class, 'alumno_id');
    }

    public function canalizaciones(): HasMany
    {
        return $this->hasMany(Canalizacion::class, 'alumno_id');
    }
}
