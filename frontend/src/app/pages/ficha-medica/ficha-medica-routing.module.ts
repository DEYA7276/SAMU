import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { FichaMedicaPage } from './ficha-medica.page';

const routes: Routes = [
  {
    path: '',
    component: FichaMedicaPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class FichaMedicaPageRoutingModule {}
