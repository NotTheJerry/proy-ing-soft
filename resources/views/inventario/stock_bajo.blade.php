@extends('layouts.app')

@section('titulo', 'Stock Bajo')

@section('actions')
<div class="btn-group me-2">
    <a href="{{ route('inventario.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Volver al Inventario
    </a>
</div>
<div class="btn-group me-2">
    <a href="#" class="btn btn-sm btn-outline-danger">
        <i class="fas fa-bell me-1"></i> Generar Alertas
    </a>
</div>
@endsection

@section('contenido')
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-warning" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> Esta vista muestra productos con un stock menor a 10 unidades que necesitan reposición.
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Productos con Stock Bajo</h5>                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-export me-1"></i> Exportar Reporte
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('inventario.reporte', ['formato' => 'pdf']) }}"><i class="fas fa-file-pdf me-1"></i> Exportar como PDF</a></li>
                        <li><a class="dropdown-item" href="{{ route('inventario.reporte', ['formato' => 'excel']) }}"><i class="fas fa-file-excel me-1"></i> Exportar como Excel</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" onclick="window.print(); return false;"><i class="fas fa-print me-1"></i> Imprimir esta página</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Proveedor</th>
                                <th class="text-center">Stock</th>
                                <th>Punto de Reorden</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($inventarios as $inventario)
                                <tr>
                                    <td>{{ $inventario->producto->id_producto }}</td>
                                    <td>
                                        <strong>{{ $inventario->producto->descripcion }}</strong>
                                        <div class="small text-muted">{{ $inventario->producto->precio }} €</div>
                                    </td>
                                    <td>{{ $inventario->producto->categoria->nombre }}</td>
                                    <td>
                                        <a href="#" data-bs-toggle="tooltip" data-bs-title="Ver detalles del proveedor">
                                            {{ $inventario->producto->proveedor->nombre }}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        @if($inventario->cantidad_disponible <= 0)
                                            <span class="badge bg-danger">Agotado</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ $inventario->cantidad_disponible }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            // Simular un punto de reorden
                                            $puntoReorden = 10;
                                        @endphp
                                        {{ $puntoReorden }}
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary ordenarModal" data-bs-toggle="modal" data-bs-target="#ordenCompraModal" data-id="{{ $inventario->id_inventario }}" data-producto="{{ $inventario->producto->descripcion }}">
                                                <i class="fas fa-truck-loading"></i> Ordenar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No hay productos con stock bajo</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div>Mostrando {{ $inventarios->firstItem() ?? 0 }} a {{ $inventarios->lastItem() ?? 0 }} de {{ $inventarios->total() }} productos</div>
                    <div>{{ $inventarios->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear orden de compra -->
<div class="modal fade" id="ordenCompraModal" tabindex="-1" aria-labelledby="ordenCompraModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ordenCompraModalLabel">Crear Orden de Compra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formOrdenCompra">
                    <input type="hidden" id="inventario_id" name="inventario_id">
                    
                    <div class="mb-3">
                        <label for="producto_nombre" class="form-label">Producto</label>
                        <input type="text" class="form-control" id="producto_nombre" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cantidad" class="form-label">Cantidad a ordenar</label>
                        <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" value="20" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="proveedor_id" class="form-label">Proveedor</label>
                        <select class="form-select" id="proveedor_id" name="proveedor_id" required>
                            <option value="">Seleccione un proveedor</option>
                            <!-- Aquí se llenarían los proveedores dinámicamente desde la base de datos -->
                            <option value="1">Proveedor A</option>
                            <option value="2">Proveedor B</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="fecha_entrega" class="form-label">Fecha estimada de entrega</label>
                        <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnCrearOrden">Crear Orden</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Manejar el modal de orden de compra
        document.querySelectorAll('.ordenarModal').forEach(function(button) {
            button.addEventListener('click', function() {
                var id = this.getAttribute('data-id');
                var producto = this.getAttribute('data-producto');
                
                document.getElementById('inventario_id').value = id;
                document.getElementById('producto_nombre').value = producto;
                
                // Establecer fecha de entrega estimada (7 días a partir de hoy)
                var fechaEntrega = new Date();
                fechaEntrega.setDate(fechaEntrega.getDate() + 7);
                
                var mes = (fechaEntrega.getMonth() + 1).toString().padStart(2, '0');
                var dia = fechaEntrega.getDate().toString().padStart(2, '0');
                var fechaFormateada = fechaEntrega.getFullYear() + '-' + mes + '-' + dia;
                
                document.getElementById('fecha_entrega').value = fechaFormateada;
            });
        });
        
        // Botón crear orden
        document.getElementById('btnCrearOrden').addEventListener('click', function() {
            // Aquí iría la lógica para crear la orden de compra
            alert('Orden de compra creada correctamente');
            var modal = bootstrap.Modal.getInstance(document.getElementById('ordenCompraModal'));
            modal.hide();
        });
    });
</script>
@endsection
