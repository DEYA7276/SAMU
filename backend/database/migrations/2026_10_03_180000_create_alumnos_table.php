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
        Schema::create('alumnos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            // Matrícula y fotografía administradas exclusivamente por enfermería
            $table->string('matricula', 50)->nullable()->unique();
            $table->string('foto_alumno')->nullable();
            
            // Datos generales y personales
            $table->string('nombre_completo');
            $table->enum('sexo', ['M', 'F', 'Otro'])->nullable();
            $table->unsignedTinyInteger('edad')->nullable();
            $table->string('carrera')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('correo')->nullable();
            $table->text('domicilio')->nullable();
            
            // Contacto de emergencia
            $table->string('contacto_emergencia_nombre')->nullable();
            $table->string('contacto_emergencia_telefono', 20)->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumnos');
    }
};
