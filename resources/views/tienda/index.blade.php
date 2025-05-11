@extends('layouts.app')

@section('titulo', 'Tienda de Productos')

@section('contenido')
<div class="container my-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="display-5 fw-bold text-primary">Tienda de Productos</h1>
            <p class="lead">Explora nuestra variedad de productos disponibles</p>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Filtrar y ordenar</h5>
                    <form action="{{ route('tienda.index') }}" method="GET" class="mb-2">
                        <div class="mb-3">
                            <label class="form-label">Categoría</label>
                            <select class="form-select" name="categoria" onchange="this.form.submit()">
                                <option value="0">Todas las categorías</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ request()->categoria == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Ordenar por</label>
                            <select class="form-select" name="orden" onchange="this.form.submit()">
                                <option value="nuevo" {{ $orden == 'nuevo' ? 'selected' : '' }}>Más recientes</option>
                                <option value="precio_asc" {{ $orden == 'precio_asc' ? 'selected' : '' }}>Precio: menor a mayor</option>
                                <option value="precio_desc" {{ $orden == 'precio_desc' ? 'selected' : '' }}>Precio: mayor a menor</option>
                            </select>
                        </div>
                    </form>
                    <hr>
                    <div class="d-grid">
                        <a href="{{ route('tienda.carrito') }}" class="btn btn-primary">
                            <i class="bi bi-cart"></i> Ver carrito 
                            @if(session('carrito') && count(session('carrito')) > 0)
                                <span class="badge bg-white text-primary">{{ count(session('carrito')) }}</span>
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    @if($productos->isEmpty())
        <div class="alert alert-info" role="alert">
            <h4 class="alert-heading">¡No hay productos disponibles!</h4>
            <p>No se encontraron productos que coincidan con los criterios seleccionados.</p>
            <hr>
            <p class="mb-0">Intenta con otra categoría o revisa más tarde.</p>
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
            @foreach($productos as $producto)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ $producto->descripcion }}</h5>
                            <p class="card-text fw-bold text-primary">${{ number_format($producto->precio, 2) }}</p>                            <p class="card-text">
                                <span class="badge bg-success">Disponible: {{ $producto->inventario->cantidad_disponible }}</span>
                            </p>                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('tienda.producto', $producto->id_producto) }}" class="btn btn-outline-primary">
                                    Ver detalles
                                </a>
                                <form action="{{ route('tienda.carrito.agregar') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id_producto" value="{{ $producto->id_producto }}">
                                    <input type="hidden" name="cantidad" value="1">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $productos->links() }}
        </div>
    @endif
</div>
@endsection
