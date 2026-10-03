import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { LoadingController, ToastController } from '@ionic/angular';
import { AuthService } from '../../core/services/auth.service';
import { ApiService } from '../../core/services/api.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss'],
  standalone: false
})
export class LoginPage implements OnInit {
  segment: 'login' | 'registro' = 'login';
  showPassword = false;
  isLoading = false;

  loginForm!: FormGroup;
  registroForm!: FormGroup;

  carrerasUTGZ = [
    'Tecnologías de la Información',
    'Mantenimiento Industrial',
    'Operaciones Comerciales Internacionales',
    'Contaduría',
    'Turismo',
    'Procesos Industriales',
    'Química'
  ];

  constructor(
    private fb: FormBuilder,
    private router: Router,
    private authService: AuthService,
    private api: ApiService,
    private loadingCtrl: LoadingController,
    private toastCtrl: ToastController
  ) {}

  ngOnInit(): void {
    this.initForms();
    // Si ya tiene sesión activa, redirigir según su rol
    if (this.authService.isAuthenticated()) {
      this.redirectByRole(this.authService.getRol());
    }
  }

  private initForms(): void {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]]
    });

    // Formulario de Alumno (Regla 4: No se pide matrícula ni foto)
    this.registroForm = this.fb.group({
      name: ['', [Validators.required, Validators.minLength(3)]],
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]],
      carrera: ['', [Validators.required]],
      telefono: ['', [Validators.pattern('^[0-9]{10}$')]],
      sexo: ['M', [Validators.required]],
      edad: [18, [Validators.required, Validators.min(15), Validators.max(99)]],
      contacto_emergencia_nombre: [''],
      contacto_emergencia_telefono: ['']
    });
  }

  async onLogin(): Promise<void> {
    if (this.loginForm.invalid) {
      this.loginForm.markAllAsTouched();
      this.showToast('Por favor completa todos los campos requeridos correctamente.', 'warning');
      return;
    }

    const loader = await this.loadingCtrl.create({
      message: 'Iniciando sesión...',
      spinner: 'crescent'
    });
    await loader.present();

    this.authService.login(this.loginForm.value).subscribe({
      next: (res: any) => {
        loader.dismiss();
        this.showToast(`¡Bienvenido(a), ${res.usuario.name}!`, 'success');
        this.redirectByRole(res.usuario.rol);
      },
      error: (err: any) => {
        loader.dismiss();
        const msg = err.error?.message || 'Error al iniciar sesión. Verifica tu correo y contraseña.';
        this.showToast(msg, 'danger');
      }
    });
  }

  async onRegistro(): Promise<void> {
    if (this.registroForm.invalid) {
      this.registroForm.markAllAsTouched();
      this.showToast('Por favor verifica los campos obligatorios del registro.', 'warning');
      return;
    }

    const loader = await this.loadingCtrl.create({
      message: 'Creando cuenta de alumno...',
      spinner: 'crescent'
    });
    await loader.present();

    this.api.post<any>('registro-alumno', this.registroForm.value).subscribe({
      next: (res: any) => {
        loader.dismiss();
        // Guardar sesión tras registro exitoso
        if (res.data?.token && res.data?.usuario) {
          localStorage.setItem('samu_token', res.data.token);
          localStorage.setItem('samu_usuario', JSON.stringify(res.data.usuario));
        }
        this.showToast('¡Cuenta creada con éxito! Ahora puedes capturar tu ficha médica.', 'success');
        this.router.navigate(['/ficha-medica']);
      },
      error: (err: any) => {
        loader.dismiss();
        const msg = err.error?.message || 'No se pudo completar el registro.';
        this.showToast(msg, 'danger');
      }
    });
  }

  private redirectByRole(rol: string | null): void {
    switch (rol) {
      case 'enfermeria':
      case 'admin':
        this.router.navigate(['/enfermeria']);
        break;
      case 'jefatura':
        this.router.navigate(['/canalizaciones']);
        break;
      case 'alumno':
      default:
        this.router.navigate(['/ficha-medica']);
        break;
    }
  }

  // Cuentas de demostración para agilizar pruebas
  setDemoUser(role: 'enfermeria' | 'alumno'): void {
    if (role === 'enfermeria') {
      this.loginForm.patchValue({
        email: 'enfermeria@utgz.edu.mx',
        password: 'password'
      });
    } else {
      this.loginForm.patchValue({
        email: 'alumno.juan@utgz.edu.mx',
        password: 'password'
      });
    }
    this.showToast(`Credenciales de prueba para ${role} cargadas.`, 'medium');
  }

  togglePassword(): void {
    this.showPassword = !this.showPassword;
  }

  private async showToast(message: string, color: 'success' | 'danger' | 'warning' | 'medium'): Promise<void> {
    const toast = await this.toastCtrl.create({
      message,
      duration: 3000,
      position: 'top',
      color
    });
    await toast.present();
  }
}
