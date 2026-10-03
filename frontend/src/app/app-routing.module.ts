import { NgModule } from '@angular/core';
import { PreloadAllModules, RouterModule, Routes } from '@angular/router';
import { AuthGuard } from './core/guards/auth.guard';
import { RoleGuard } from './core/guards/role.guard';

const routes: Routes = [
  {
    path: '',
    redirectTo: 'login',
    pathMatch: 'full'
  },
  {
    path: 'login',
    loadChildren: () => import('./pages/login/login.module').then(m => m.LoginPageModule)
  },
  {
    path: 'ficha-medica',
    loadChildren: () => import('./pages/ficha-medica/ficha-medica.module').then(m => m.FichaMedicaPageModule),
    canActivate: [AuthGuard]
  },
  {
    path: 'enfermeria',
    loadChildren: () => import('./pages/enfermeria/enfermeria.module').then(m => m.EnfermeriaPageModule),
    canActivate: [AuthGuard, RoleGuard],
    data: { roles: ['enfermeria', 'admin'] }
  },
  {
    path: '**',
    redirectTo: 'login'
  }
];

@NgModule({
  imports: [
    RouterModule.forRoot(routes, { preloadingStrategy: PreloadAllModules })
  ],
  exports: [RouterModule]
})
export class AppRoutingModule { }
