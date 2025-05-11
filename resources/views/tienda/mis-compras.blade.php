@extends('layouts.app')

@section('titulo', 'Mis Compras')

@section('contenido')
<div class="container my-4">
    <h1 class="mb-4 text-primary">Mis Compras</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($compras->isEmpty())
        <div class="alert alert-info" role="alert">
            <h4 class="alert-heading">¡No tienes compras registradas!</h4>
            <p>Aún no has realizado ninguna compra en nuestra tienda.</p>
            <hr>
            <p class="mb-0">
                <a href="{{ route('tienda.index') }}" class="btn btn-primary">
                    <i class="bi bi-cart-plus"></i> Ir a comprar
                </a>
            </p>
        </div>
    @else
        <div class="row">
            <div class="col-lg-12">
                <div class="accordion" id="accordionCompras">
                    @foreach($compras as $compra)
                        <div class="accordion-item mb-3 shadow-sm">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#compra{{ $compra->id }}" aria-expanded="false"
                                    aria-controls="compra{{ $compra->id }}">
                                    <div class="d-flex justify-content-between w-100 pe-5">
                                        <div>
                                            <strong>Compra #{{ $compra->id }}</strong>
                                        </div>
                                        <div>
                                            {{ date('d/m/Y H:i', strtotime($compra->fecha)) }}
                                        </div>
                                        <div>
                                            <strong class="text-primary">${{ number_format(ceil($compra->total_venta), 2) }}</strong>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="compra{{ $compra->id }}" class="accordion-collapse collapse"
                                data-bs-parent="#accordionCompras">
                                <div class="accordion-body">
                                    <div class="table-responsive">
                                        <table class="table align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Producto</th>
                                                    <th class="text-center">Cantidad</th>
                                                    <th class="text-end">Precio unitario</th>
                                                    <th class="text-end">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($compra->detalles as $detalle)
                                                    <tr>
                                                        <td>{{ $detalle->producto->descripcion ?? 'Producto no disponible' }}</td>
                                                        <td class="text-center">{{ $detalle->cantidad }}</td>
                                                        <td class="text-end">${{ number_format($detalle->precio_unitario, 2) }}</td>
                                                        <td class="text-end">${{ number_format($detalle->subtotal, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" class="text-end">
                                                        <strong>Total:</strong>
                                                    </td>
                                                    <td class="text-end">
                                                        {{-- <strong>${{ number_format($detalle->subtotal, 2) }}</strong> --}}
                                                        <strong>${{ number_format(ceil($compra->total_venta), 2) }}</strong>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $compras->links() }}
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('tienda.index') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-left"></i> Volver a la tienda
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
