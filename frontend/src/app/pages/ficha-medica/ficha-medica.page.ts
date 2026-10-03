import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AlertController, LoadingController, ToastController } from '@ionic/angular';
import { ApiService } from '../../core/services/api.service';

@Component({
  selector: 'app-ficha-medica',
  templateUrl: './ficha-medica.page.html',
  styleUrls: ['./ficha-medica.page.scss'],
  standalone: false
})
export class FichaMedicaPage implements OnInit, AfterViewInit {
  @ViewChild('signatureCanvas') canvasRef!: ElementRef<HTMLCanvasElement>;

  fichaForm!: FormGroup;
  currentStep = 1;
  totalSteps = 6;
  isSubmitted = false;
  folioGenerado: string | null = null;
  alumnoRegistrado: any = null;

  // Antecedentes Familiares (Opciones)
  opcionesFamiliares = [
    { label: 'Diabetes Mellitus', selected: false },
    { label: 'Hipertensión Arterial', selected: false },
    { label: 'Cardiopatías / Problemas del corazón', selected: false },
    { label: 'Cáncer', selected: false },
    { label: 'Asma / Alergias respiratorias', selected: false },
    { label: 'Enfermedades Renales', selected: false },
    { label: 'Ninguno de los anteriores', selected: false }
  ];

  // Antecedentes Personales (Opciones)
  opcionesPersonales = [
    { label: 'Asma', selected: false },
    { label: 'Rinitis Alérgica', selected: false },
    { label: 'Gastritis / Reflujo', selected: false },
    { label: 'Epilepsia / Convulsiones', selected: false },
    { label: 'Problemas de Columna / Posturales', selected: false },
    { label: 'Problemas de Vista (Usa lentes)', selected: false },
    { label: 'Ninguno de los anteriores', selected: false }
  ];

  // Canvas para firma digital
  private canvas!: HTMLCanvasElement;
  private ctx!: CanvasRenderingContext2D | null;
  private isDrawing = false;
  hasSignature = false;

  constructor(
    private fb: FormBuilder,
    private router: Router,
    private api: ApiService,
    private loadingCtrl: LoadingController,
    private toastCtrl: ToastController,
    private alertCtrl: AlertController
  ) {}

  ngOnInit(): void {
    this.initForm();
  }

  ngAfterViewInit(): void {
    setTimeout(() => this.initCanvas(), 300);
  }

  private initForm(): void {
    this.fichaForm = this.fb.group({
      // Paso 1: Identificación (Sin matrícula, sin foto, sin correo obligatorio, sin carrera)
      nombre_completo: ['', [Validators.required, Validators.minLength(3)]],
      telefono: ['', [Validators.pattern('^[0-9]{10}$')]],
      contacto_emergencia_nombre: [''],
      contacto_emergencia_telefono: ['', [Validators.pattern('^[0-9]{10}$')]],

      // Paso 2: Antecedentes (se gestionan con checkboxes)
      antecedentes_familiares_otros: [''],
      antecedentes_personales_otros: [''],

      // Paso 3: Antecedentes médicos
      cirugias: ['Ninguna'],
      traumatismos: ['Ninguno'],
      alergias: ['Ninguna'],

      // Paso 4: Situación médica actual
      padecimientos: ['Ninguno'],
      tratamiento_medico: [''],
      control_preventivo: [''],
      medicamentos_restringidos: [''],

      // Paso 5: Médico particular
      nombre_medico: [''],
      telefono_medico: [''],
      hospital_preferencia: [''],

      // Paso 6: Autorización del tutor
      nombre_tutor: ['', [Validators.required, Validators.minLength(3)]],
      autorizacion_imss: [true, [Validators.requiredTrue]]
    });
  }

  // Navegación entre pasos del formulario
  setStep(step: number): void {
    if (step > this.currentStep) {
      if (this.currentStep === 1 && this.fichaForm.get('nombre_completo')?.invalid) {
        this.showToast('Ingresa tu nombre completo para continuar.', 'warning');
        return;
      }
    }
    this.currentStep = step;
    if (step === 6) {
      setTimeout(() => this.initCanvas(), 200);
    }
  }

  nextStep(): void {
    if (this.currentStep < this.totalSteps) {
      this.setStep(this.currentStep + 1);
    }
  }

  prevStep(): void {
    if (this.currentStep > 1) {
      this.setStep(this.currentStep - 1);
    }
  }

  // ================= MANEJO DEL CANVAS DE FIRMA DIGITAL =================
  private initCanvas(): void {
    if (!this.canvasRef?.nativeElement) return;
    this.canvas = this.canvasRef.nativeElement;
    this.ctx = this.canvas.getContext('2d');

    if (this.ctx) {
      // Ajustar resolución según tamaño del contenedor
      const rect = this.canvas.getBoundingClientRect();
      this.canvas.width = rect.width || 320;
      this.canvas.height = 140;

      this.ctx.strokeStyle = '#0f172a';
      this.ctx.lineWidth = 2.5;
      this.ctx.lineCap = 'round';
      this.ctx.lineJoin = 'round';
    }
  }

  startDrawing(event: MouseEvent | TouchEvent): void {
    event.preventDefault();
    this.isDrawing = true;
    const pos = this.getCanvasPosition(event);
    if (this.ctx) {
      this.ctx.beginPath();
      this.ctx.moveTo(pos.x, pos.y);
    }
  }

  draw(event: MouseEvent | TouchEvent): void {
    if (!this.isDrawing || !this.ctx) return;
    event.preventDefault();
    const pos = this.getCanvasPosition(event);
    this.ctx.lineTo(pos.x, pos.y);
    this.ctx.stroke();
    this.hasSignature = true;
  }

  stopDrawing(): void {
    this.isDrawing = false;
  }

  clearSignature(): void {
    if (this.ctx && this.canvas) {
      this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
      this.hasSignature = false;
    }
  }

  private getCanvasPosition(event: MouseEvent | TouchEvent): { x: number; y: number } {
    const rect = this.canvas.getBoundingClientRect();
    if (event instanceof MouseEvent) {
      return {
        x: event.clientX - rect.left,
        y: event.clientY - rect.top
      };
    } else if (event.touches && event.touches.length > 0) {
      return {
        x: event.touches[0].clientX - rect.left,
        y: event.touches[0].clientY - rect.top
      };
    }
    return { x: 0, y: 0 };
  }

  // ================= ENVÍO DE LA FICHA MÉDICA =================
  async onSubmit(): Promise<void> {
    if (this.fichaForm.invalid) {
      this.fichaForm.markAllAsTouched();
      this.showToast('Por favor verifica los campos obligatorios antes de enviar.', 'warning');
      return;
    }

    const loader = await this.loadingCtrl.create({
      message: 'Registrando tu Ficha Médica...',
      spinner: 'crescent'
    });
    await loader.present();

    // Recopilar antecedentes seleccionados
    const antFam = this.opcionesFamiliares
      .filter(o => o.selected)
      .map(o => o.label);
    const otrosFam = this.fichaForm.get('antecedentes_familiares_otros')?.value;
    if (otrosFam) antFam.push(`Otros: ${otrosFam}`);

    const antPer = this.opcionesPersonales
      .filter(o => o.selected)
      .map(o => o.label);
    const otrosPer = this.fichaForm.get('antecedentes_personales_otros')?.value;
    if (otrosPer) antPer.push(`Otros: ${otrosPer}`);

    // Extraer firma en Base64 si existe
    let firmaBase64 = null;
    if (this.hasSignature && this.canvas) {
      firmaBase64 = this.canvas.toDataURL('image/png');
    }

    const payload = {
      nombre_completo: this.fichaForm.get('nombre_completo')?.value,
      telefono: this.fichaForm.get('telefono')?.value || null,
      contacto_emergencia_nombre: this.fichaForm.get('contacto_emergencia_nombre')?.value || null,
      contacto_emergencia_telefono: this.fichaForm.get('contacto_emergencia_telefono')?.value || null,
      antecedentes_familiares: antFam,
      antecedentes_personales: antPer,
      cirugias: this.fichaForm.get('cirugias')?.value || 'Ninguna',
      traumatismos: this.fichaForm.get('traumatismos')?.value || 'Ninguno',
      alergias: this.fichaForm.get('alergias')?.value || 'Ninguna',
      padecimientos: this.fichaForm.get('padecimientos')?.value || 'Ninguno',
      tratamiento_medico: this.fichaForm.get('tratamiento_medico')?.value || null,
      control_preventivo: this.fichaForm.get('control_preventivo')?.value || null,
      medicamentos_restringidos: this.fichaForm.get('medicamentos_restringidos')?.value || null,
      nombre_medico: this.fichaForm.get('nombre_medico')?.value || null,
      telefono_medico: this.fichaForm.get('telefono_medico')?.value || null,
      hospital_preferencia: this.fichaForm.get('hospital_preferencia')?.value || null,
      nombre_tutor: this.fichaForm.get('nombre_tutor')?.value,
      firma_tutor: firmaBase64,
      autorizacion_imss: this.fichaForm.get('autorizacion_imss')?.value
    };

    this.api.post<any>('fichas-medicas', payload).subscribe({
      next: (res: any) => {
        loader.dismiss();
        this.isSubmitted = true;
        this.folioGenerado = res.data?.folio || 'SAMU-FOLIO';
        this.alumnoRegistrado = res.data?.alumno;
      },
      error: (err: any) => {
        loader.dismiss();
        const msg = err.error?.message || 'No se pudo guardar la ficha médica. Intenta nuevamente.';
        this.showToast(msg, 'danger');
      }
    });
  }

  volverAlInicio(): void {
    this.router.navigate(['/login']);
  }

  private async showToast(message: string, color: 'success' | 'danger' | 'warning' | 'medium'): Promise<void> {
    const toast = await this.toastCtrl.create({
      message,
      duration: 3500,
      position: 'top',
      color
    });
    await toast.present();
  }
}
