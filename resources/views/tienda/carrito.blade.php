@extends('layouts.app')

@section('titulo', 'Carrito de Compras')

@section('contenido')
<div class="container my-4">
    <h1 class="mb-4 text-primary">Carrito de Compras</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {!! session('success') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {!! session('error') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {!! session('warning') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {!! session('info') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(isset($debug_info) && count($debug_info) > 0)
        <div class="alert alert-secondary">
            <h4>Información de depuración:</h4>
            <ul>
                @foreach($debug_info as $info)
                    <li>{!! $info !!}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(empty($carrito))
        <div class="alert alert-info" role="alert">
            <h4 class="alert-heading">¡Tu carrito está vacío!</h4>
            <p>No tienes productos en tu carrito de compras.</p>
            <hr>
            <p class="mb-0">
                <a href="{{ route('tienda.index') }}" class="btn btn-primary">
                    <i class="bi bi-cart-plus"></i> Ir a comprar
                </a>
            </p>
        </div>
    @else
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Productos en el carrito</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('tienda.carrito.actualizar') }}" method="POST">
                            @csrf
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Precio</th>
                                            <th width="140">Cantidad</th>
                                            <th class="text-end">Subtotal</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>                                        @foreach($carrito as $item)
                                            <tr>
                                                <td>
                                                    @if(isset($item['producto']) && $item['producto'])
                                                        <a href="{{ route('tienda.producto', $item['producto']->id_producto) }}">
                                                            {{ $item['producto']->descripcion }}
                                                        </a>
                                                    @else
                                                        <span class="text-danger">Producto no disponible</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(isset($item['producto']) && $item['producto'])
                                                        ${{ number_format($item['producto']->precio, 2) }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(isset($item['producto']) && $item['producto'] && isset($item['producto']->inventario))
                                                        <input type="number" class="form-control" 
                                                            name="cantidades[{{ $item['id_producto'] }}]"
                                                            value="{{ $item['cantidad'] }}" 
                                                            min="1" max="{{ $item['producto']->inventario->cantidad_disponible }}" 
                                                            required>
                                                    @else
                                                        <span class="text-danger">No disponible</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    @if(isset($item['subtotal']))
                                                        ${{ number_format($item['subtotal'], 2) }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    <form action="{{ route('tienda.carrito.eliminar', $item['id_producto']) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-clockwise"></i> Actualizar carrito
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Resumen de compra</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <h5>Total:</h5>
                            <h5>${{ number_format($total, 2) }}</h5>
                        </div>
                        <form action="{{ route('tienda.comprar') }}" method="POST">
                            @csrf
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-credit-card"></i> Procesar compra
                                </button>
                                <a href="{{ route('tienda.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Seguir comprando
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Métodos de pago</h5>
                    </div>
                    <div class="card-body">
                        <p><i class="bi bi-credit-card"></i> Tarjeta de crédito/débito</p>
                        <p><i class="bi bi-paypal"></i> PayPal</p>
                        <p><i class="bi bi-bank"></i> Transferencia bancaria</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
