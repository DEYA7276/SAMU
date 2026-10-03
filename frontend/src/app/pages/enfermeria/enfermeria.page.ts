import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';
import { Usuario } from '../../shared/models/samu.models';

@Component({
  selector: 'app-enfermeria',
  templateUrl: './enfermeria.page.html',
  styleUrls: ['./enfermeria.page.scss'],
  standalone: false
})
export class EnfermeriaPage implements OnInit {
  usuario: Usuario | null = null;

  constructor(
    private authService: AuthService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.usuario = this.authService.currentUserValue;
  }

  onLogout(): void {
    this.authService.logout().subscribe(() => {
      this.router.navigate(['/login']);
    });
  }
}
