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
        Schema::create('fichas_medicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            
            // FASE 1 — Captura por el Alumno desde su celular
            $table->string('nombre_completo');
            
            // Antecedentes (almacenados en JSON para flexibilidad de checkboxes)
            $table->json('antecedentes_familiares')->nullable();
            $table->json('antecedentes_personales')->nullable();
            
            // Antecedentes médicos
            $table->text('cirugias')->nullable();
            $table->text('traumatismos')->nullable();
            $table->text('alergias')->nullable();
            
            // Situación médica actual
            $table->text('padecimientos')->nullable();
            $table->text('tratamiento_medico')->nullable();
            $table->text('control_preventivo')->nullable();
            $table->text('medicamentos_restringidos')->nullable();
            
            // Médico particular
            $table->string('nombre_medico')->nullable();
            $table->string('telefono_medico', 20)->nullable();
            $table->string('hospital_preferencia')->nullable();
            
            // Autorización del tutor
            $table->string('nombre_tutor');
            $table->longText('firma_tutor')->nullable(); // Imagen Base64 o vector de firma
            $table->boolean('autorizacion_imss')->default(false);
            
            // FASE 2 — Validación y complemento por Enfermería
            $table->enum('estatus_validacion', ['pendiente_validacion', 'validada', 'requiere_ajuste'])
                  ->default('pendiente_validacion');
            $table->text('observaciones_enfermeria')->nullable();
            $table->foreignId('validado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_validacion')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fichas_medicas');
    }
};
