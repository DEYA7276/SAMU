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
        Schema::create('canalizaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('atencion_id')->nullable()->constrained('atenciones')->nullOnDelete();
            
            // Datos de canalización
            $table->text('motivo'); // Captura manual del motivo
            $table->date('fecha');  // Generada automáticamente
            $table->time('hora');   // Generada automáticamente
            
            // Personal que elaboró
            $table->foreignId('elaborado_por')->constrained('users');
            
            // Firmas digitales
            $table->longText('firma_enfermera')->nullable();
            $table->longText('firma_alumno')->nullable();
            
            // Gestión de PDF y envío a jefatura de carrera
            $table->string('pdf_path')->nullable();
            $table->string('jefatura_carrera')->nullable(); // Carrera a la que pertenece el jefe
            $table->boolean('enviado_a_jefatura')->default(false);
            $table->timestamp('fecha_envio_jefatura')->nullable();
            $table->enum('estatus', ['emitida', 'enviada', 'recibida_jefatura'])->default('emitida');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('canalizaciones');
    }
};
