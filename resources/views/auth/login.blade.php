@extends('layouts.layout')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card card">
        <div class="auth-header">
            <h1>Iniciar sesión</h1>
            <p>Accede a tu cuenta para continuar</p>
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

            <form method="POST" action="/login">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror"
                        required autofocus placeholder="tu@email.com">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input id="password" type="password" name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        required placeholder="••••••••">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label text-muted" for="remember">Recordarme</label>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary btn-lg">Entrar →</button>
                </div>
            </form>

            <p class="text-center mb-0 text-muted" style="font-size:.88rem">
                ¿No tienes cuenta?
                <a href="/register" class="fw-semibold">Regístrate</a>
            </p>
        </div>
    </div>
</div>
@endsection
