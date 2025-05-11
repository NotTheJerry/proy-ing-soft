@extends('layouts.app')

@section('titulo', 'Error')

@section('contenido')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">{{ isset($rol_actual) ? 'Error de Acceso' : 'Error en la Aplicación' }}</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <h4 class="alert-heading">{{ isset($rol_actual) ? '¡Acceso Denegado!' : '¡Ha ocurrido un error!' }}</h4>
                        <p>{{ $mensaje }}</p>
                        @if(isset($rol_actual))
                            <hr>
                            <p class="mb-0">Tu rol actual es: <strong>{{ $rol_actual }}</strong></p>
                        @endif
                    </div>
                    
                    @if(isset($debug) && Auth::check() && Auth::user()->role == 'admin')
                        <div class="mt-4 alert alert-warning">
                            <h5>Información de depuración (solo para administradores):</h5>
                            <p><strong>Archivo:</strong> {{ $debug['file'] }}</p>
                            <p><strong>Línea:</strong> {{ $debug['line'] }}</p>
                            <div>
                                <strong>Traza:</strong>
                                <pre class="small mt-2" style="max-height: 300px; overflow-y: auto;">{{ $debug['trace'] }}</pre>
                            </div>
                        </div>
                    @endif
                    
                    <div class="mt-4 text-center">
                        <a href="{{ route('tienda.index') }}" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>Volver a la Tienda
                        </a>
                        
                        @if(Auth::check() && Auth::user()->role == 'admin')
                            <a href="{{ route('inventario.index') }}" class="btn btn-secondary ms-2">
                                <i class="fas fa-boxes me-2"></i>Ir a Inventario
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
