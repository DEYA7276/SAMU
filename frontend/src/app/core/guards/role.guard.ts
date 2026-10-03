import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

@Injectable({
  providedIn: 'root'
})
export class RoleGuard implements CanActivate {
  constructor(private authService: AuthService, private router: Router) {}

  canActivate(route: ActivatedRouteSnapshot): boolean {
    const expectedRoles: string[] = route.data['roles'] || [];
    const userRole = this.authService.getRol();

    if (!userRole || (expectedRoles.length > 0 && !expectedRoles.includes(userRole))) {
      this.router.navigate(['/login']);
      return false;
    }

    return true;
  }
}
