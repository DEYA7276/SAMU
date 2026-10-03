<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Catálogo e inventario de insumos médicos
        Schema::create('insumos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_insumo');
            $table->text('descripcion')->nullable();
            $table->date('fecha_caducidad');
            $table->integer('cantidad_inicio')->default(0);
            $table->integer('cantidad_actual')->default(0);
            $table->string('cuatrimestre'); // Ej: "Enero - Abril 2026"
            $table->timestamps();
        });

        // Registro de entradas y salidas de inventario
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insumo_id')->constrained('insumos')->cascadeOnDelete();
            $table->enum('tipo_movimiento', ['entrada', 'salida', 'ajuste']);
            $table->integer('cantidad');
            $table->string('motivo')->nullable();
            $table->foreignId('registrado_por')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
        Schema::dropIfExists('insumos');
    }
};
