# SISTEMA INTEGRAL DE SERVICIOS MÉDICOS ESTUDIANTILES — SAMU UTGZ

## CONTEXTO MAESTRO PARA IA

Este archivo contiene el contexto funcional y técnico del proyecto.
Debe utilizarse como fuente principal para comprender el sistema antes de generar código, arquitectura, interfaces, base de datos o documentación.

---

### 1. IDENTIDAD DEL PROYECTO

- **Nombre:** Sistema Integral de Servicios Médicos Estudiantiles de la UTGZ
- **Nombre corto:** SAMU
- **Institución:** Universidad Tecnológica de Gutiérrez Zamora (UTGZ)

El proyecto busca digitalizar el proceso de atención médica estudiantil de la universidad, sustituyendo procesos manuales y basados en papel por un sistema digital centralizado.

La aplicación será desarrollada principalmente como una app con **Ionic + Angular**, consumiendo una **API REST desarrollada en Laravel**, con **MySQL** como base de datos.

La arquitectura será **cliente-servidor desacoplada**.

---

### 2. OBJETIVO GENERAL

Desarrollar un sistema que permita digitalizar:
- El registro de información médica de los alumnos.
- La ficha médica inicial.
- La validación y complemento de información por parte de enfermería.
- La bitácora de atención médica.
- La generación de canalizaciones.
- La generación de documentos PDF.
- El envío digital de canalizaciones a jefes de carrera.
- El control básico de inventario de insumos médicos.

El objetivo es reducir el uso de papel, mejorar la organización de la información y optimizar el proceso de atención y canalización de alumnos.

---

### 3. OBJETIVOS ESPECÍFICOS

- Implementar un formulario digital para que el alumno registre su información médica.
- Permitir que enfermería complemente y valide los datos del alumno.
- Desarrollar una bitácora digital de atención médica.
- Automatizar la generación de canalizaciones en formato PDF.
- Facilitar el envío digital de canalizaciones a jefes de carrera.
- Implementar un control básico de inventario de insumos médicos.
- Mejorar la organización y consulta de información médica.

---

### 4. ALCANCE

#### El sistema SÍ incluye:
- Registro de ficha médica del alumno.
- Captura inicial desde el celular del estudiante.
- Complemento de matrícula por enfermería.
- Captura/subida de fotografía del alumno.
- Validación de información por enfermería.
- Bitácora de atención médica.
- Registro automático de fecha y hora.
- Generación de canalizaciones.
- Generación de PDF.
- Envío digital de documentos.
- Control básico de inventario.
- Expediente digital del alumno.
- Autorizaciones correspondientes.

#### El sistema NO incluye:
- Historial clínico avanzado de tipo hospitalario.
- Integración con sistemas externos de salud.
- Diagnósticos automáticos.
- Inteligencia artificial para diagnóstico médico.

---

### 5. ARQUITECTURA TECNOLÓGICA

#### Frontend
**Ionic + Angular**
- App multiplataforma (móvil y compatibilidad web).
- Ionic: Interfaces, formularios, navegación, componentes móviles, adaptación responsiva.
- Angular: Componentes, servicios, guards, manejo de estado, comunicación con la API REST, modularidad.

#### Backend
**Laravel API REST**
- Responsabilidades: Autenticación (Sanctum/JWT), autorización, validación de datos, lógica de negocio, gestión de usuarios, fichas médicas, bitácora, canalizaciones, documentos PDF, inventario, comunicación con MySQL.

#### Base de datos
**MySQL**
- Almacenamiento estructurado: Usuarios, alumnos, fichas médicas, antecedentes, atenciones, canalizaciones, firmas, inventario y registros relacionados.

#### Comunicación
```
Ionic + Angular
       |
       | HTTP / JSON
       v
Laravel API REST
       |
       v
     MySQL
```

---

### 6. FLUJO GENERAL DEL SISTEMA

```
ALUMNO
  |
  | 1. Llena ficha médica desde su celular
  v
FICHA MÉDICA
  |
  | 2. Enfermería revisa
  v
ENFERMERÍA
  |
  | 3. Agrega matrícula y fotografía
  | 4. Valida información
  v
EXPEDIENTE DIGITAL
  |
  | 5. El alumno recibe atención
  v
BITÁCORA DE ATENCIÓN
  |
  | 6. Si es necesario canalizar
  v
CANALIZACIÓN
  |
  | 7. Generación de PDF
  v
DOCUMENTO PDF
  |
  | 8. Envío
  v
JEFE DE CARRERA
```

Paralelamente:
```
ATENCIÓN MÉDICA
      |
      v
USO DE INSUMOS
      |
      v
INVENTARIO
```

---

### 7. CAMBIO IMPORTANTE EN EL FLUJO DE FICHA MÉDICA

- **Flujo anterior:** Enfermería llenaba toda la información.
- **Flujo actual y definitivo:** El alumno llena su propia información médica desde su celular.
- **Después:**
  - Enfermería revisa la información.
  - Enfermería agrega la matrícula.
  - Enfermería agrega la fotografía.
  - Enfermería valida los datos.
  - Enfermería puede realizar ajustes cuando sea necesario.
- **Regla crucial:** La matrícula y la fotografía **NO** se solicitan inicialmente al alumno dentro de la ficha médica. Pertenecen a la fase de complemento de enfermería.

---

### 8. MÓDULO DE FICHA MÉDICA (Fase 1 — Alumno)

Captura desde el celular del estudiante:
- **Datos generales:** `nombre_completo`
- **Antecedentes:** `antecedentes_familiares`, `antecedentes_personales` (checkboxes/opciones claras).
- **Antecedentes médicos:** `cirugias`, `traumatismos`, `alergias`.
- **Situación médica actual:** `padecimientos`, `tratamiento_medico`, `control_preventivo`, `medicamentos_restringidos`.
- **Médico particular:** `nombre_medico`, `telefono_medico`, `hospital_preferencia`.
- **Autorización del tutor:** `nombre_tutor`, `firma_tutor`, `autorizacion_imss`.

---

### 9. FASE 2 — ENFERMERÍA

- Complementa `matricula` y `foto_alumno`.
- Valida datos y realiza ajustes necesarios.

---

### 10. CAMPOS FINALES DE LA FICHA MÉDICA

- **Identificación (Enfermería):** `id_alumno`, `matricula`, `foto_alumno`.
- **Datos médicos (Alumno):** `nombre_completo`, `antecedentes_familiares`, `antecedentes_personales`, `cirugias`, `traumatismos`, `alergias`, `situacion_medica_actual`, `datos_medico_particular`, `autorizacion_tutor`.

---

### 11. MÓDULO DE BITÁCORA DE ATENCIÓN

- **Campos:** `alumno_id`, `fecha` (automática), `hora` (automática), `motivo_consulta`, `padecimiento`, `atencion_brindada`, `observaciones`, `firma_alumno`, `firma_enfermeria`.
- **Reglas:** Fecha y hora automáticas; cada atención asociada a un alumno; historial de visitas persistente.

---

### 12. MÓDULO DE CANALIZACIÓN

- **Datos del alumno:** `alumno_id`, `nombre_completo`, `matricula`, `sexo`, `edad`, `carrera`, `telefono`, `correo`, `domicilio`.
- **Contacto de emergencia:** `contacto_emergencia_nombre`, `contacto_emergencia_telefono`.
- **Datos de canalización:** `motivo` (captura manual), `fecha` (automática), `hora` (automática), `elaborado_por`.
- **Firmas:** `firma_enfermera`, `firma_alumno`.
- **Generación:** Documento en PDF descargable y envío digital a jefes de carrera.

---

### 13. MÓDULO DE INVENTARIO

- **Campos:** `nombre_insumo`, `descripcion`, `fecha_caducidad`, `cantidad_inicio`, `cantidad_fin`, `cuatrimestre`.
- **Operaciones:** Registro de insumos, entradas, salidas, cálculo de stock disponible, vinculado al uso de insumos en atenciones.

---

### 14. ROLES DEL SISTEMA

1. **Alumno:** Registro y captura de ficha médica inicial, autorizaciones, consulta de expediente propio, firma en bitácora/canalización.
2. **Enfermería:** Búsqueda y gestión de alumnos, revisión y validación de fichas, asignación de matrícula y foto, registro de atenciones en bitácora, emisión y firma de canalizaciones, gestión de inventario.
3. **Jefatura de carrera:** Recepción y consulta de canalizaciones emitidas para alumnos de su carrera.

---

### 15. SEGURIDAD Y DATOS MÉDICOS

- Información altamente sensible.
- Autenticación y autorización por roles estrictos en backend (Laravel Sanctum/JWT).
- Validación estricta en backend (no confiar sólo en frontend).
- Protección de rutas y endpoints de expedientes.
- Validación de archivos y firmas subidas.
- **Regla de oro:** Jamás subir datos médicos reales al repositorio ni a entornos públicos. Usar sólo datos ficticios de prueba (seeders/factories).

---

### 16. ESTRUCTURA DE ARQUITECTURA RECOMENDADA

#### Frontend (Ionic + Angular)
```
frontend/
└── src/
    ├── app/
    │   ├── core/
    │   │   ├── guards/
    │   │   ├── interceptors/
    │   │   └── services/
    │   ├── shared/
    │   │   ├── components/
    │   │   ├── models/
    │   │   └── utilities/
    │   ├── pages/
    │   │   ├── login/
    │   │   ├── alumno/
    │   │   ├── ficha-medica/
    │   │   ├── enfermeria/
    │   │   ├── bitacora/
    │   │   ├── canalizaciones/
    │   │   └── inventario/
    │   └── app.routes.ts
    └── assets/
```

#### Backend (Laravel API REST)
```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Requests/
│   │   └── Resources/
│   ├── Models/
│   └── Services/
│       ├── AlumnoService.php
│       ├── FichaMedicaService.php
│       ├── AtencionMedicaService.php
│       ├── CanalizacionService.php
│       └── InventarioService.php
├── database/
│   ├── migrations/
│   └── seeders/
└── routes/
    └── api.php
```

---

### 17. CONTROL DE VERSIONES Y TRABAJO EN EQUIPO

- **Equipo:**
  - Integrante 1: Deyanira Gallardo García
  - Integrante 2: Compañera de equipo
- **Estructura de ramas:**
  - `main`: Versión estable protegida (sólo mediante Pull Request final, sin push directo).
  - `develop`: Rama de integración principal.
  - `feature/*`: Nuevas funcionalidades (ej. `feature/ficha-medica`).
  - `fix/*`: Correcciones de errores.
  - `chore/*`: Tareas de configuración y mantenimiento.
- **Flujo:** `feature/*` / `fix/*` → Pull Request → `develop` → Pruebas → Pull Request → `main`.
- **Commits:** Semánticos y descriptivos (`feat: ...`, `fix: ...`, `chore: ...`, `docs: ...`, `refactor: ...`).

---

### 18. REGLAS ESENCIALES PARA LA IA

1. **No cambiar la arquitectura:** Ionic + Angular (Frontend) + Laravel API REST (Backend) + MySQL (BD).
2. **No hospitalizar:** Mantener alcance de servicios médicos escolares UTGZ (no diagnósticos complejos, IA médica ni UCI).
3. **Flujo estricto:** Alumno llena datos médicos → Enfermería revisa, asigna matrícula y foto, y valida.
4. **No pedir matrícula al alumno en ficha inicial.**
5. **No inventar funcionalidades fuera de alcance.**
6. **Seguridad y validaciones duplicadas en backend.**
7. **Código limpio, tipado, modular y con Service Layer en Laravel y Singleton Services en Angular.**
8. **Nombres consistentes en español técnico:** `alumno`, `ficha_medica`, `atencion`, `canalizacion`, `inventario`, `usuario`, `enfermeria`.
9. **Nunca subir credenciales reales ni datos médicos reales al repositorio.**
