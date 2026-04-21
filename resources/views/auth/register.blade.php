@extends('layouts.layout')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 96px);">
    <div class="card shadow-sm" style="width: 100%; max-width: 460px;">
        <div class="card-body p-4">
            <h2 class="card-title mb-4 text-center fw-bold">Crear cuenta</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
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
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        class="form-control @error('name') is-invalid @enderror"
                        required
                        autofocus
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror"
                        required
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        required
                    >
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        class="form-control"
                        required
                    >
                </div>

                {{-- Admin checkbox --}}
                <div class="mb-4 p-3 border rounded bg-light">
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="is_admin"
                            id="is_admin"
                            value="1"
                            {{ old('is_admin') ? 'checked' : '' }}
                        >
                        <label class="form-check-label fw-semibold" for="is_admin">
                            ¿Este usuario es administrador?
                        </label>
                        <div class="form-text">Si marcas esta opción, se asignará el rol <strong>admin</strong>.</div>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary">Registrarse</button>
                </div>
            </form>

            <p class="text-center mb-0">
                ¿Ya tienes cuenta?
                <a href="/login">Inicia sesión</a>
            </p>
        </div>
    </div>
</div>
@endsection
