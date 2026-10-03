# SAMU UTGZ — Sistema Integral de Servicios Médicos Estudiantiles

> **Universidad Tecnológica de Gutiérrez Zamora (UTGZ)**  
> Sistema digital centralizado para la atención médica, ficha clínica estudiantil, bitácora, canalizaciones en PDF y control de inventario médico.

---

## 📋 Descripción del Proyecto

El **SAMU UTGZ** sustituye los procesos manuales y en papel de los servicios médicos universitarios por una plataforma moderna y desacoplada:

1. **Ficha Médica Estudiantil:** Registro directo por parte del alumno desde su dispositivo móvil (sin matrícula ni foto en fase inicial).
2. **Validación de Enfermería:** El personal de enfermería revisa los datos, asigna la matrícula oficial, adjunta la fotografía y valida la ficha.
3. **Bitácora de Atención Médica:** Registro automático de fecha y hora, motivo de consulta, diagnóstico, tratamiento e insumos utilizados con firma digital.
4. **Canalizaciones y PDF:** Generación automatizada de oficios de canalización médica en PDF con firmas y envío digital a jefaturas de carrera.
5. **Control de Inventario:** Monitoreo de entradas, salidas y existencias de insumos médicos por cuatrimestre.

---

## 🛠️ Stack Tecnológico

| Capa | Tecnología | Propósito |
| :--- | :--- | :--- |
| **Frontend** | **Ionic 7+ & Angular 17+** | Aplicación móvil multiplataforma y web responsiva |
| **Backend** | **Laravel 10+ / 11+** | API REST, Autenticación (Sanctum), Lógica de negocio y PDF |
| **Base de Datos** | **MySQL** | Persistencia relacional estructurada |
| **Nativo** | **Capacitor** | Soporte de cámara y capacidades nativas móviles |
| **Control de Versiones** | **Git & GitHub** | Flujo Git Flow con ramas protegidas y Pull Requests |

---

## 👥 Equipo de Desarrollo

| Integrante | Rol |
| :--- | :--- |
| **Deyanira Gallardo García** | Desarrollo Frontend / Backend |
| **[Nombre de la Compañera]** | Desarrollo Frontend / Backend |

---

## 🌿 Convenciones de Git y Flujo de Trabajo

El proyecto utiliza una estrategia basada en **Git Flow**:

```
main (Producción / Estable - Protegida)
  ▲
  │ (Pull Request final tras pruebas)
develop (Integración continua)
  ▲
  ├── feature/nombre-funcionalidad (Nuevas características)
  ├── fix/nombre-del-error         (Correcciones de bugs)
  └── chore/nombre-tarea           (Configuraciones / Dependencias)
```

### Reglas Clave:
- ❌ **Nunca hacer push directo a `main` ni a `develop`.**
- ✅ Crear siempre una rama a partir de `develop`:
  ```bash
  git checkout develop
  git pull origin develop
  git checkout -b feature/nombre-funcionalidad
  ```
- ✅ Realizar commits semánticos y atómicos:
  - `feat: agregar formulario de ficha médica`
  - `fix: corregir cálculo de insumos`
  - `chore: configurar cors y sanctum`
- ✅ Subir la rama y abrir **Pull Request** hacia `develop` para revisión entre las dos integrantes.

---

## 📂 Estructura del Repositorio

```
SAMU_PROYECTO/
├── .gitignore
├── README.md
├── CONTEXTO_MAESTRO.md          # Especificación y reglas maestras de SAMU
├── backend/                     # API REST en Laravel
│   ├── app/
│   │   ├── Http/Controllers/
│   │   ├── Models/
│   │   └── Services/            # Service Layer (lógica desacoplada)
│   ├── database/
│   └── routes/api.php
└── frontend/                    # App móvil y web en Ionic + Angular
    └── src/
        ├── app/
        │   ├── core/            # Servicios singleton, guards, interceptores
        │   ├── shared/          # Componentes y modelos compartidos
        │   └── pages/           # Vistas y pantallas (Alumno, Enfermería, etc.)
        └── assets/
```

---

## 🔒 Privacidad y Seguridad de Datos Médicos

- Los datos de salud de los alumnos son información altamente confidencial.
- **Prohibido:** Subir contraseñas, tokens, archivos `.env` o datos médicos reales al repositorio.
- Todas las pruebas deben ejecutarse con **datos ficticios** generados por factories/seeders.
- Todas las validaciones de frontend se replican estrictamente en el backend.
