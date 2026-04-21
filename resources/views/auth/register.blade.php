@extends('layouts.layout')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card card">
        <div class="auth-header">
            <h1>Crear cuenta</h1>
            <p>Únete a Mi Tienda hoy</p>
        </div>
        <div class="auth-body">
            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/register">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Nombre</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}"
                        class="form-control @error('name') is-invalid @enderror"
                        required autofocus placeholder="Tu nombre">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror"
                        required placeholder="tu@email.com">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input id="password" type="password" name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        required placeholder="Mínimo 8 caracteres">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                        class="form-control" required placeholder="Repite tu contraseña">
                </div>

                {{-- Admin checkbox --}}
                <div class="mb-4 p-3 rounded" style="background:#eef2ff; border:1.5px solid #c7d2fe;">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_admin"
                            id="is_admin" value="1" {{ old('is_admin') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_admin"
                            style="color:#3730a3;">
                            ¿Este usuario es administrador?
                        </label>
                        <div class="form-text text-muted" style="font-size:.8rem;">
                            Si marcas esta opción se asignará el rol <strong>admin</strong>.
                        </div>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary btn-lg">Registrarse →</button>
                </div>
            </form>

            <p class="text-center mb-0 text-muted" style="font-size:.88rem">
                ¿Ya tienes cuenta?
                <a href="/login" class="fw-semibold">Inicia sesión</a>
            </p>
        </div>
    </div>
</div>
@endsection
