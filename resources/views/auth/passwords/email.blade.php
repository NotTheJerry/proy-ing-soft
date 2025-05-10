@extends('layouts.auth')

@section('titulo', 'Restablecer Contraseña')

@section('contenido')
<div class="container">
    <div class="row justify-content-center mb-4">
        <div class="col-md-6 text-center">
            <h1 class="display-4 fw-bold text-primary">Sistema de Gestión</h1>
            <p class="lead">Inventario y Control de Productos</p>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Solicitar Restablecimiento de Contraseña</h4>
                </div>
                <div class="card-body p-4">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                Enviar Enlace de Restablecimiento
                            </button>
                        </div>
                    </form>

                    <div class="mt-3 text-center">
                        <a href="{{ route('login') }}" class="text-decoration-none">Volver a Iniciar Sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
