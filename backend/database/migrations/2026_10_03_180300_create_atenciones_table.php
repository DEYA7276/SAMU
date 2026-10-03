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
        // Bitácora de atención médica
        Schema::create('atenciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('enfermera_id')->constrained('users'); // Personal que atiende
            
            // Fecha y hora generadas automáticamente
            $table->date('fecha');
            $table->time('hora');
            
            // Datos clínicos de la consulta
            $table->text('motivo_consulta');
            $table->text('padecimiento');
            $table->text('atencion_brindada');
            $table->text('observaciones')->nullable();
            
            // Firmas digitales
            $table->longText('firma_alumno')->nullable();
            $table->longText('firma_enfermeria')->nullable();
            
            $table->boolean('requiere_canalizacion')->default(false);
            $table->timestamps();
        });

        // Insumos utilizados durante la atención médica (descuento de inventario)
        Schema::create('atencion_insumos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atencion_id')->constrained('atenciones')->cascadeOnDelete();
            $table->foreignId('insumo_id')->constrained('insumos');
            $table->integer('cantidad')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atencion_insumos');
        Schema::dropIfExists('atenciones');
    }
};
