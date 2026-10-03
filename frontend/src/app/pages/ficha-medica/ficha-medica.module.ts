import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { IonicModule } from '@ionic/angular/lazy';

import { FichaMedicaPageRoutingModule } from './ficha-medica-routing.module';
import { FichaMedicaPage } from './ficha-medica.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    IonicModule,
    FichaMedicaPageRoutingModule
  ],
  declarations: [FichaMedicaPage]
})
export class FichaMedicaPageModule {}
