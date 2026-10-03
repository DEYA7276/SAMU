<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Atencion extends Model
{
    use HasFactory;

    protected $table = 'atenciones';

    protected $fillable = [
        'alumno_id',
        'enfermera_id',
        'fecha',
        'hora',
        'motivo_consulta',
        'padecimiento',
        'atencion_brindada',
        'observaciones',
        'firma_alumno',
        'firma_enfermeria',
        'requiere_canalizacion',
    ];

    protected $casts = [
        'fecha' => 'date',
        'requiere_canalizacion' => 'boolean',
    ];

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }

    public function enfermera(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enfermera_id');
    }

    public function insumosUtilizados(): HasMany
    {
        return $this->hasMany(AtencionInsumo::class, 'atencion_id');
    }

    public function canalizacion(): HasOne
    {
        return $this->hasOne(Canalizacion::class, 'atencion_id');
    }
}
