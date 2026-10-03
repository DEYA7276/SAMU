import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable, tap } from 'rxjs';
import { ApiService } from './api.service';
import { AuthResponse, Usuario } from 'src/app/shared/models/samu.models';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private readonly TOKEN_KEY = 'samu_token';
  private readonly USER_KEY = 'samu_usuario';

  private currentUserSubject = new BehaviorSubject<Usuario | null>(this.getStoredUser());
  public currentUser$ = this.currentUserSubject.asObservable();

  constructor(private api: ApiService) {}

  private getStoredUser(): Usuario | null {
    const raw = localStorage.getItem(this.USER_KEY);
    if (!raw) return null;
    try {
      return JSON.parse(raw);
    } catch {
      return null;
    }
  }

  public get currentUserValue(): Usuario | null {
    return this.currentUserSubject.value;
  }

  public isAuthenticated(): boolean {
    return !!localStorage.getItem(this.TOKEN_KEY) && !!this.currentUserValue;
  }

  public getRol(): string | null {
    return this.currentUserValue?.rol || null;
  }

  login(credentials: { email: string; password: string }): Observable<AuthResponse> {
    return this.api.post<AuthResponse>('login', credentials).pipe(
      tap(res => {
        if (res.token && res.usuario) {
          localStorage.setItem(this.TOKEN_KEY, res.token);
          localStorage.setItem(this.USER_KEY, JSON.stringify(res.usuario));
          this.currentUserSubject.next(res.usuario);
        }
      })
    );
  }

  logout(): Observable<any> {
    return this.api.post('logout', {}).pipe(
      tap({
        next: () => this.clearSession(),
        error: () => this.clearSession()
      })
    );
  }

  clearSession(): void {
    localStorage.removeItem(this.TOKEN_KEY);
    localStorage.removeItem(this.USER_KEY);
    this.currentUserSubject.next(null);
  }
}
