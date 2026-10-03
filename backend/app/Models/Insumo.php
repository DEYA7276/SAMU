<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Insumo extends Model
{
    use HasFactory;

    protected $table = 'insumos';

    protected $fillable = [
        'nombre_insumo',
        'descripcion',
        'fecha_caducidad',
        'cantidad_inicio',
        'cantidad_actual',
        'cuatrimestre',
    ];

    protected $casts = [
        'fecha_caducidad' => 'date',
        'cantidad_inicio' => 'integer',
        'cantidad_actual' => 'integer',
    ];

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'insumo_id');
    }

    public function atencionesInsumos(): HasMany
    {
        return $this->hasMany(AtencionInsumo::class, 'insumo_id');
    }
}
