# Modelo Entidad-Relación y Diccionario de Datos — SAMU UTGZ

Este documento describe la estructura relacional de la base de datos MySQL implementada en Laravel para el **Sistema Integral de Servicios Médicos Estudiantiles de la UTGZ**.

---

## 1. Diagrama Entidad-Relación (Mermaid)

```mermaid
erDiagram
    USERS ||--o| ALUMNOS : "tiene cuenta de acceso (opcional)"
    USERS ||--o{ ATENCIONES : "atiende (enfermera)"
    USERS ||--o{ CANALIZACIONES : "elabora (enfermera)"
    USERS ||--o{ MOVIMIENTOS_INVENTARIO : "registra entrada/salida"
    USERS ||--o{ FICHAS_MEDICAS : "valida (enfermera)"

    ALUMNOS ||--|| FICHAS_MEDICAS : "tiene una"
    ALUMNOS ||--o{ ATENCIONES : "recibe"
    ALUMNOS ||--o{ CANALIZACIONES : "se le emite"

    ATENCIONES ||--o| CANALIZACIONES : "puede originar"
    ATENCIONES ||--o{ ATENCION_INSUMOS : "utiliza"
    INSUMOS ||--o{ ATENCION_INSUMOS : "se consume en"
    INSUMOS ||--o{ MOVIMIENTOS_INVENTARIO : "tiene movimientos"

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        enum rol "alumno, enfermeria, jefatura, admin"
        string carrera "nullable"
    }

    ALUMNOS {
        bigint id PK
        bigint user_id FK "nullable"
        string matricula UK "nullable (asignada por enfermería)"
        string foto_alumno "nullable (subida por enfermería)"
        string nombre_completo
        enum sexo "M, F, Otro"
        tinyint edad
        string carrera
        string telefono
        string correo
        text domicilio
        string contacto_emergencia_nombre
        string contacto_emergencia_telefono
    }

    FICHAS_MEDICAS {
        bigint id PK
        bigint alumno_id FK
        string nombre_completo
        json antecedentes_familiares
        json antecedentes_personales
        text cirugias
        text traumatismos
        text alergias
        text padecimientos
        text tratamiento_medico
        text control_preventivo
        text medicamentos_restringidos
        string nombre_medico
        string telefono_medico
        string hospital_preferencia
        string nombre_tutor
        longtext firma_tutor
        boolean autorizacion_imss
        enum estatus_validacion "pendiente_validacion, validada, requiere_ajuste"
        text observaciones_enfermeria
        bigint validado_por FK "nullable"
        timestamp fecha_validacion "nullable"
    }

    ATENCIONES {
        bigint id PK
        bigint alumno_id FK
        bigint enfermera_id FK
        date fecha "automática"
        time hora "automática"
        text motivo_consulta
        text padecimiento
        text atencion_brindada
        text observaciones
        longtext firma_alumno
        longtext firma_enfermeria
        boolean requiere_canalizacion
    }

    INSUMOS {
        bigint id PK
        string nombre_insumo
        text descripcion
        date fecha_caducidad
        int cantidad_inicio
        int cantidad_actual
        string cuatrimestre
    }

    MOVIMIENTOS_INVENTARIO {
        bigint id PK
        bigint insumo_id FK
        enum tipo_movimiento "entrada, salida, ajuste"
        int cantidad
        string motivo
        bigint registrado_por FK
    }

    ATENCION_INSUMOS {
        bigint id PK
        bigint atencion_id FK
        bigint insumo_id FK
        int cantidad
    }

    CANALIZACIONES {
        bigint id PK
        bigint alumno_id FK
        bigint atencion_id FK "nullable"
        text motivo
        date fecha "automática"
        time hora "automática"
        bigint elaborado_por FK
        longtext firma_enfermera
        longtext firma_alumno
        string pdf_path
        string jefatura_carrera
        boolean enviado_a_jefatura
        timestamp fecha_envio_jefatura
        enum estatus "emitida, enviada, recibida_jefatura"
    }
```

---

## 2. Reglas de Integridad y Lógica de Negocio

1. **Flujo de Ficha Médica:**
   - La tabla `alumnos` permite `matricula` y `foto_alumno` nulos inicialmente porque el alumno no los proporciona en la fase inicial desde su celular.
   - Enfermería es quien actualiza la matrícula, fotografía y cambia `estatus_validacion` a `validada` en `fichas_medicas`.
2. **Bitácora y Consumo de Insumos:**
   - Cada registro en `atenciones` almacena automáticamente `fecha` y `hora`.
   - Cuando se suministran medicamentos o materiales (ej. paracetamol, gasas), se registra en `atencion_insumos` y genera un registro de salida en `movimientos_inventario`, descontando de `insumos.cantidad_actual`.
3. **Canalizaciones y Jefatura:**
   - Las canalizaciones se asocian al alumno para extraer automáticamente sus datos de contacto y emergencia.
   - El campo `jefatura_carrera` permite a los usuarios con rol `jefatura` filtrar únicamente los alumnos de su respectiva carrera.
