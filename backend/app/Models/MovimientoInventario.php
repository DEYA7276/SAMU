<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoInventario extends Model
{
    use HasFactory;

    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'insumo_id',
        'tipo_movimiento',
        'cantidad',
        'motivo',
        'registrado_por',
    ];

    protected $casts = [
        'cantidad' => 'integer',
    ];

    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class, 'insumo_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
