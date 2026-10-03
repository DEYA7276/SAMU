<?php

namespace Database\Seeders;

use App\Models\Alumno;
use App\Models\Atencion;
use App\Models\AtencionInsumo;
use App\Models\Canalizacion;
use App\Models\FichaMedica;
use App\Models\Insumo;
use App\Models\MovimientoInventario;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with fictional data for development.
     */
    public function run(): void
    {
        // -------------------------------------------------------------
        // 1. Usuarios con roles (contraseña de prueba: password)
        // -------------------------------------------------------------
        $enfermera = User::create([
            'name' => 'Lic. María Elena Ramos (Enfermería)',
            'email' => 'enfermeria@utgz.edu.mx',
            'password' => Hash::make('password'),
            'rol' => 'enfermeria',
            'carrera' => null,
        ]);

        $jefeTics = User::create([
            'name' => 'Ing. Roberto Hernández (Jefe TI)',
            'email' => 'jefe.ti@utgz.edu.mx',
            'password' => Hash::make('password'),
            'rol' => 'jefatura',
            'carrera' => 'Tecnologías de la Información',
        ]);

        $userAlumno1 = User::create([
            'name' => 'Juan Carlos Pérez Mendoza',
            'email' => 'alumno.juan@utgz.edu.mx',
            'password' => Hash::make('password'),
            'rol' => 'alumno',
            'carrera' => 'Tecnologías de la Información',
        ]);

        $userAlumno2 = User::create([
            'name' => 'Ana Sofía Domínguez García',
            'email' => 'alumno.ana@utgz.edu.mx',
            'password' => Hash::make('password'),
            'rol' => 'alumno',
            'carrera' => 'Mantenimiento Industrial',
        ]);

        // -------------------------------------------------------------
        // 2. Alumnos
        // -------------------------------------------------------------
        // Alumno 1: Ya validado por enfermería (con matrícula y foto ficticia)
        $alumno1 = Alumno::create([
            'user_id' => $userAlumno1->id,
            'matricula' => 'UTGZ-2024-0012',
            'foto_alumno' => null,
            'nombre_completo' => 'Juan Carlos Pérez Mendoza',
            'sexo' => 'M',
            'edad' => 20,
            'carrera' => 'Tecnologías de la Información',
            'telefono' => '7841002030',
            'correo' => 'alumno.juan@utgz.edu.mx',
            'domicilio' => 'Calle 5 de Mayo #12, Gutiérrez Zamora, Ver.',
            'contacto_emergencia_nombre' => 'Rosa Mendoza (Madre)',
            'contacto_emergencia_telefono' => '7841112233',
        ]);

        // Ficha médica validada de Alumno 1
        FichaMedica::create([
            'alumno_id' => $alumno1->id,
            'nombre_completo' => $alumno1->nombre_completo,
            'antecedentes_familiares' => ['Diabetes Tipo 2', 'Hipertensión Arterial'],
            'antecedentes_personales' => ['Rinitis alérgica'],
            'cirugias' => 'Apendicectomía (2020)',
            'traumatismos' => 'Esguince de tobillo derecho (2022)',
            'alergias' => 'Penicilina, Polvo',
            'padecimientos' => 'Asma estacional controlada',
            'tratamiento_medico' => 'Salbutamol inhalador en caso de crisis',
            'control_preventivo' => 'Consulta anual con neumólogo',
            'medicamentos_restringidos' => 'Penicilinas y derivados',
            'nombre_medico' => 'Dr. Fernando Morales Silva',
            'telefono_medico' => '7841234567',
            'hospital_preferencia' => 'Hospital Integral Gutiérrez Zamora',
            'nombre_tutor' => 'Rosa Mendoza López',
            'firma_tutor' => 'firma_digital_base64_ejemplo',
            'autorizacion_imss' => true,
            'estatus_validacion' => 'validada',
            'observaciones_enfermeria' => 'Alumno acudió con carnet IMSS vigente y comprobante de tutor.',
            'validado_por' => $enfermera->id,
            'fecha_validacion' => Carbon::now()->subDays(5),
        ]);

        // Alumno 2: Flujo inicial (llenó ficha desde celular, SIN matrícula ni foto aún)
        $alumno2 = Alumno::create([
            'user_id' => $userAlumno2->id,
            'matricula' => null, // Respeto estricto al flujo: enfermería la asigna después
            'foto_alumno' => null,
            'nombre_completo' => 'Ana Sofía Domínguez García',
            'sexo' => 'F',
            'edad' => 19,
            'carrera' => 'Mantenimiento Industrial',
            'telefono' => '7841445566',
            'correo' => 'alumno.ana@utgz.edu.mx',
            'domicilio' => 'Av. Independencia #45, Tecolutla, Ver.',
            'contacto_emergencia_nombre' => 'Carlos Domínguez (Padre)',
            'contacto_emergencia_telefono' => '7841778899',
        ]);

        // Ficha médica pendiente de validación de Alumno 2
        FichaMedica::create([
            'alumno_id' => $alumno2->id,
            'nombre_completo' => $alumno2->nombre_completo,
            'antecedentes_familiares' => ['Ninguno'],
            'antecedentes_personales' => ['Gastritis'],
            'cirugias' => 'Ninguna',
            'traumatismos' => 'Ninguno',
            'alergias' => 'Ninguna conocida',
            'padecimientos' => 'Dolor de cabeza tensional recurrente',
            'tratamiento_medico' => 'Omeprazol 20mg ocasional',
            'control_preventivo' => 'Ninguno',
            'medicamentos_restringidos' => 'Ninguno',
            'nombre_medico' => 'Dra. Patricia Salazar',
            'telefono_medico' => '7841998877',
            'hospital_preferencia' => 'Centro de Salud Tecolutla',
            'nombre_tutor' => 'Carlos Domínguez Martínez',
            'firma_tutor' => 'firma_digital_base64_ejemplo_2',
            'autorizacion_imss' => true,
            'estatus_validacion' => 'pendiente_validacion',
            'observaciones_enfermeria' => null,
            'validado_por' => null,
            'fecha_validacion' => null,
        ]);

        // -------------------------------------------------------------
        // 3. Catálogo de Insumos Médicos
        // -------------------------------------------------------------
        $cuatrimestreActual = 'Mayo - Agosto 2026';

        $insumosData = [
            ['nombre_insumo' => 'Paracetamol 500mg', 'descripcion' => 'Caja con 20 tabletas analgésicas y antipiréticas', 'fecha_caducidad' => '2027-10-31', 'cantidad_inicio' => 100, 'cantidad_actual' => 95],
            ['nombre_insumo' => 'Ibuprofeno 400mg', 'descripcion' => 'Caja con 10 cápsulas antiinflamatorias', 'fecha_caducidad' => '2027-08-15', 'cantidad_inicio' => 80, 'cantidad_actual' => 78],
            ['nombre_insumo' => 'Vendas elásticas 10cm', 'descripcion' => 'Rollo de venda elástica para compresión', 'fecha_caducidad' => '2028-12-31', 'cantidad_inicio' => 40, 'cantidad_actual' => 38],
            ['nombre_insumo' => 'Alcohol etílico al 70%', 'descripcion' => 'Frasco de 500 ml para desinfección', 'fecha_caducidad' => '2028-06-30', 'cantidad_inicio' => 15, 'cantidad_actual' => 14],
            ['nombre_insumo' => 'Gasas estériles 10x10', 'descripcion' => 'Sobre individual estéril', 'fecha_caducidad' => '2029-01-01', 'cantidad_inicio' => 200, 'cantidad_actual' => 185],
            ['nombre_insumo' => 'Curitas adhesivas', 'descripcion' => 'Caja con 100 apósitos adhesivos', 'fecha_caducidad' => '2028-05-20', 'cantidad_inicio' => 300, 'cantidad_actual' => 290],
            ['nombre_insumo' => 'Abatelenguas de madera', 'descripcion' => 'Paquete con 100 piezas desechables', 'fecha_caducidad' => '2030-01-01', 'cantidad_inicio' => 500, 'cantidad_actual' => 495],
            ['nombre_insumo' => 'Termómetro digital', 'descripcion' => 'Termómetro axilar infrarrojo/digital', 'fecha_caducidad' => '2031-12-31', 'cantidad_inicio' => 8, 'cantidad_actual' => 8],
        ];

        $insumosCreados = [];
        foreach ($insumosData as $item) {
            $insumo = Insumo::create(array_merge($item, ['cuatrimestre' => $cuatrimestreActual]));
            $insumosCreados[$item['nombre_insumo']] = $insumo;

            // Registrar movimiento inicial de entrada
            MovimientoInventario::create([
                'insumo_id' => $insumo->id,
                'tipo_movimiento' => 'entrada',
                'cantidad' => $item['cantidad_inicio'],
                'motivo' => 'Inventario inicial del cuatrimestre ' . $cuatrimestreActual,
                'registrado_por' => $enfermera->id,
            ]);
        }

        // -------------------------------------------------------------
        // 4. Bitácora de Atención Médica (Ejemplo de consulta)
        // -------------------------------------------------------------
        $atencion1 = Atencion::create([
            'alumno_id' => $alumno1->id,
            'enfermera_id' => $enfermera->id,
            'fecha' => Carbon::now()->toDateString(),
            'hora' => Carbon::now()->format('H:i:s'),
            'motivo_consulta' => 'Cefalea intensa y mareos leves tras clase matutina',
            'padecimiento' => 'Cefalea tensional por fatiga visual y deshidratación leve',
            'atencion_brindada' => 'Toma de signos vitales (T/A 110/70, Temp 36.4°C). Reposo de 20 min en camilla. Se administró 1 tableta de Paracetamol 500mg y rehidratación oral.',
            'observaciones' => 'El alumno refiere mejoría notable a los 25 minutos. Se retira a su domicilio con recomendación de descanso.',
            'firma_alumno' => 'firma_digital_alumno_ejemplo',
            'firma_enfermeria' => 'firma_digital_enfermera_ejemplo',
            'requiere_canalizacion' => false,
        ]);

        // Registrar uso de insumos en la consulta
        if (isset($insumosCreados['Paracetamol 500mg'])) {
            AtencionInsumo::create([
                'atencion_id' => $atencion1->id,
                'insumo_id' => $insumosCreados['Paracetamol 500mg']->id,
                'cantidad' => 1,
            ]);

            MovimientoInventario::create([
                'insumo_id' => $insumosCreados['Paracetamol 500mg']->id,
                'tipo_movimiento' => 'salida',
                'cantidad' => 1,
                'motivo' => 'Uso en atención médica folio #' . $atencion1->id,
                'registrado_por' => $enfermera->id,
            ]);
        }

        // -------------------------------------------------------------
        // 5. Canalización Médica (Ejemplo de canalización para Alumno 1)
        // -------------------------------------------------------------
        Canalizacion::create([
            'alumno_id' => $alumno1->id,
            'atencion_id' => $atencion1->id,
            'motivo' => 'Se sugiere valoración médica externa por cefaleas recurrentes y revisión oftalmológica.',
            'fecha' => Carbon::now()->toDateString(),
            'hora' => Carbon::now()->format('H:i:s'),
            'elaborado_por' => $enfermera->id,
            'firma_enfermera' => 'firma_digital_enfermera_ejemplo',
            'firma_alumno' => 'firma_digital_alumno_ejemplo',
            'pdf_path' => null,
            'jefatura_carrera' => 'Tecnologías de la Información',
            'enviado_a_jefatura' => true,
            'fecha_envio_jefatura' => Carbon::now(),
            'estatus' => 'enviada',
        ]);
    }
}
