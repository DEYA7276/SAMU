/**
 * Definición de interfaces y tipos de datos para SAMU UTGZ
 */

export type RolUsuario = 'alumno' | 'enfermeria' | 'jefatura' | 'admin';

export interface Usuario {
  id: number;
  name: string;
  email: string;
  rol: RolUsuario;
  created_at?: string;
  updated_at?: string;
}

export interface AuthResponse {
  token: string;
  usuario: Usuario;
}

export interface Alumno {
  id: number;
  user_id?: number;
  matricula?: string | null;
  foto_alumno?: string | null;
  nombre_completo: string;
  sexo?: string;
  edad?: number;
  carrera?: string;
  telefono?: string;
  correo?: string;
  domicilio?: string;
  contacto_emergencia_nombre?: string;
  contacto_emergencia_telefono?: string;
  ficha_medica?: FichaMedica;
}

export type EstatusFicha = 'pendiente_validacion' | 'validada' | 'requiere_ajuste';

export interface FichaMedica {
  id?: number;
  alumno_id?: number;
  // Fase 1: Datos proporcionados por el alumno
  nombre_completo: string;
  antecedentes_familiares: string | string[];
  antecedentes_personales: string | string[];
  cirugias: string;
  traumatismos: string;
  alergias: string;
  padecimientos: string;
  tratamiento_medico: string;
  control_preventivo: string;
  medicamentos_restringidos: string;
  nombre_medico?: string;
  telefono_medico?: string;
  hospital_preferencia?: string;
  nombre_tutor: string;
  firma_tutor?: string;
  autorizacion_imss: boolean;

  // Fase 2: Complemento de enfermería
  matricula?: string;
  foto_alumno?: string;
  estatus_validacion?: EstatusFicha;
  observaciones_enfermeria?: string;
  fecha_validacion?: string;
  validado_por?: number;
}

export interface InsumoUsadoItem {
  insumo_id: number;
  nombre_insumo: string;
  cantidad: number;
}

export interface AtencionMedica {
  id?: number;
  alumno_id: number;
  alumno?: Alumno;
  fecha: string; // Generada automáticamente
  hora: string;  // Generada automáticamente
  motivo_consulta: string;
  padecimiento: string;
  atencion_brindada: string;
  observaciones?: string;
  firma_alumno?: string;
  firma_enfermeria?: string;
  insumos_utilizados?: InsumoUsadoItem[];
  created_at?: string;
}

export interface Canalizacion {
  id?: number;
  alumno_id: number;
  alumno?: Alumno;
  motivo: string;
  fecha: string; // Generada automáticamente
  hora: string;  // Generada automáticamente
  elaborado_por: string;
  firma_enfermera?: string;
  firma_alumno?: string;
  pdf_url?: string;
  enviado_a_jefatura?: boolean;
  jefatura_carrera?: string;
  created_at?: string;
}

export interface Insumo {
  id?: number;
  nombre_insumo: string;
  descripcion?: string;
  fecha_caducidad: string;
  cantidad_inicio: number;
  cantidad_fin: number;
  cuatrimestre: string;
  created_at?: string;
  updated_at?: string;
}
