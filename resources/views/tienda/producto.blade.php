@extends('layouts.app')

@section('titulo', $producto->descripcion)

@section('contenido')
<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('tienda.index') }}">Tienda</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $producto->descripcion }}</li>
        </ol>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h1 class="mb-3">{{ $producto->descripcion }}</h1>
                    <p class="lead mb-4">
                        <span class="fs-3 fw-bold text-primary">${{ number_format($producto->precio, 2) }}</span>
                    </p>                    
                    <div class="mb-4">
                        <span class="badge bg-success">Disponible: {{ $producto->inventario->cantidad_disponible }} unidades</span>
                        <span class="badge bg-secondary ms-2">Categoría: {{ $producto->categoria->nombre ?? 'Sin categoría' }}</span>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Descripción del producto</h5>
                        <p>{{ $producto->descripcion }}</p>
                    </div>
                    
                    <form action="{{ route('tienda.carrito.agregar') }}" method="POST" class="mb-4">
                        @csrf
                        <input type="hidden" name="id_producto" value="{{ $producto->id }}">
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <label for="cantidad" class="form-label">Cantidad:</label>
                            </div>
                            <div class="col-auto">                                <input type="number" class="form-control" id="cantidad" name="cantidad" 
                                    min="1" max="{{ $producto->inventario->cantidad_disponible }}" value="1" style="width: 80px;">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-cart-plus"></i> Agregar al carrito
                                </button>
                            </div>
                        </div>
                    </form>

                    <a href="{{ route('tienda.carrito') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-cart"></i> Ver carrito
                        @if(session('carrito') && count(session('carrito')) > 0)
                            <span class="badge bg-primary">{{ count(session('carrito')) }}</span>
                        @endif
                    </a>
                    <a href="{{ route('tienda.index') }}" class="btn btn-outline-secondary ms-2">
                        <i class="bi bi-arrow-left"></i> Volver a la tienda
                    </a>
                </div>
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Información de envío</h5>
                        </div>
                        <div class="card-body">
                            <p><i class="bi bi-truck"></i> Envío en 2-3 días hábiles</p>
                            <p><i class="bi bi-shield-check"></i> Garantía de devolución</p>
                            <p><i class="bi bi-credit-card"></i> Pago seguro</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($relacionados->count() > 0)
    <div class="mb-4">
        <h3>Productos relacionados</h3>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            @foreach($relacionados as $relacionado)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ $relacionado->descripcion }}</h5>
                            <p class="card-text fw-bold text-primary">${{ number_format($relacionado->precio, 2) }}</p>
                            <div class="d-grid">
                                <a href="{{ route('tienda.producto', $relacionado->id) }}" class="btn btn-outline-primary">
                                    Ver detalles
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
