@extends('layouts.app')

@section('titulo', 'Gestión de Inventario')

@section('actions')
<div class="btn-group me-2">
    <a href="{{ route('inventario.stock-bajo') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-exclamation-triangle me-1"></i> Ver Stock Bajo
    </a>
</div>
<div class="btn-group me-2">
    <a href="{{ route('inventario.reporte') }}" class="btn btn-sm btn-outline-primary">
        <i class="fas fa-file-export me-1"></i> Generar Reporte
    </a>
</div>
@endsection

@section('contenido')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">Filtros de búsqueda</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('inventario.filtrar') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="categoria_id" class="form-label">Categoría</label>
                            <select class="form-select" id="categoria_id" name="categoria_id">
                                <option value="">Todas las categorías</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id_categoria }}">{{ $categoria->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="stock" class="form-label">Stock</label>
                            <select class="form-select" id="stock" name="stock">
                                <option value="">Todos</option>
                                <option value="disponible">Disponible</option>
                                <option value="agotado">Agotado</option>
                                <option value="bajo">Stock Bajo</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i> Buscar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Inventario de Productos</h5>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fas fa-file-import me-1"></i> Importar
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-file-export me-1"></i> Exportar
                    </button>
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
                                <th>Última Actualización</th>
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
                                    <td>{{ $inventario->producto->proveedor->nombre }}</td>
                                    <td class="text-center">
                                        @if($inventario->cantidad_disponible <= 0)
                                            <span class="badge bg-danger">Agotado</span>
                                        @elseif($inventario->cantidad_disponible < 10)
                                            <span class="badge bg-warning text-dark">Bajo ({{ $inventario->cantidad_disponible }})</span>
                                        @else
                                            <span class="badge bg-success">{{ $inventario->cantidad_disponible }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $inventario->fecha_ultima_actualizacion->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateModal{{ $inventario->id_inventario }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewModal{{ $inventario->id_inventario }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#historyModal{{ $inventario->id_inventario }}">
                                                <i class="fas fa-history"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Modal para actualizar el stock -->
                                        <div class="modal fade" id="updateModal{{ $inventario->id_inventario }}" tabindex="-1" aria-labelledby="updateModalLabel{{ $inventario->id_inventario }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="updateModalLabel{{ $inventario->id_inventario }}">Actualizar Stock</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('inventario.actualizar', $inventario->id_inventario) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="cantidad_disponible" class="form-label">Cantidad Disponible</label>
                                                                <input type="number" class="form-control" id="cantidad_disponible" name="cantidad_disponible" value="{{ $inventario->cantidad_disponible }}" min="0" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="motivo" class="form-label">Motivo de la actualización</label>
                                                                <select class="form-select" id="motivo" name="motivo">
                                                                    <option value="recepcion">Recepción de mercancía</option>
                                                                    <option value="ajuste">Ajuste de inventario</option>
                                                                    <option value="devolucion">Devolución</option>
                                                                    <option value="perdida">Pérdida o daño</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="observaciones" class="form-label">Observaciones</label>
                                                                <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-primary">Actualizar</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Modal para ver detalles del producto -->
                                        <div class="modal fade" id="viewModal{{ $inventario->id_inventario }}" tabindex="-1" aria-labelledby="viewModalLabel{{ $inventario->id_inventario }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="viewModalLabel{{ $inventario->id_inventario }}">Detalles del Producto</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <p><strong>ID:</strong> {{ $inventario->producto->id_producto }}</p>
                                                                <p><strong>Descripción:</strong> {{ $inventario->producto->descripcion }}</p>
                                                                <p><strong>Categoría:</strong> {{ $inventario->producto->categoria->nombre }}</p>
                                                                <p><strong>Precio:</strong> {{ $inventario->producto->precio }} €</p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p><strong>Stock:</strong> {{ $inventario->cantidad_disponible }}</p>
                                                                <p><strong>Proveedor:</strong> {{ $inventario->producto->proveedor->nombre }}</p>
                                                                <p><strong>Estado:</strong> {{ $inventario->producto->estado }}</p>
                                                                <p><strong>Última Actualización:</strong> {{ $inventario->fecha_ultima_actualizacion->format('d/m/Y') }}</p>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="row mb-3">
                                                            <div class="col-md-12">
                                                                <h6>Tallas disponibles:</h6>
                                                                <div class="mb-2">
                                                                    @if(is_array($inventario->producto->tallas))
                                                                        @foreach($inventario->producto->tallas as $talla => $disponible)
                                                                            <span class="badge {{ $disponible ? 'bg-success' : 'bg-danger' }} me-1">{{ $talla }}</span>
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                                
                                                                <h6>Colores disponibles:</h6>
                                                                <div>
                                                                    @if(is_array($inventario->producto->colores))
                                                                        @foreach($inventario->producto->colores as $color)
                                                                            <span class="badge bg-secondary me-1">{{ $color }}</span>
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                        <button type="button" class="btn btn-primary">Editar Producto</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Modal para ver historial de movimientos -->
                                        <div class="modal fade" id="historyModal{{ $inventario->id_inventario }}" tabindex="-1" aria-labelledby="historyModalLabel{{ $inventario->id_inventario }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="historyModalLabel{{ $inventario->id_inventario }}">Historial de Movimientos</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Fecha</th>
                                                                        <th>Tipo</th>
                                                                        <th>Cantidad</th>
                                                                        <th>Usuario</th>
                                                                        <th>Observaciones</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>01/05/2025</td>
                                                                        <td><span class="badge bg-success">Entrada</span></td>
                                                                        <td>+50</td>
                                                                        <td>Admin</td>
                                                                        <td>Recepción de mercancía</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>03/05/2025</td>
                                                                        <td><span class="badge bg-danger">Salida</span></td>
                                                                        <td>-5</td>
                                                                        <td>Vendedor1</td>
                                                                        <td>Venta #1052</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>05/05/2025</td>
                                                                        <td><span class="badge bg-warning text-dark">Ajuste</span></td>
                                                                        <td>-2</td>
                                                                        <td>Admin</td>
                                                                        <td>Ajuste por inventario físico</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                        <button type="button" class="btn btn-primary">Exportar Historial</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No hay productos en el inventario</td>
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

<!-- Modal Importar -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Importar Inventario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="archivo_excel" class="form-label">Seleccione archivo Excel</label>
                        <input class="form-control" type="file" id="archivo_excel" name="archivo_excel" accept=".xlsx,.xls,.csv">
                        <div class="form-text">Formatos soportados: XLSX, XLS, CSV</div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="actualizar_existentes" name="actualizar_existentes">
                            <label class="form-check-label" for="actualizar_existentes">
                                Actualizar productos existentes
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="ignorar_errores" name="ignorar_errores">
                            <label class="form-check-label" for="ignorar_errores">
                                Ignorar errores en la importación
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary">Importar</button>
            </div>
        </div>
    </div>
</div>

<!-- Dashboard Cards (Resumen) -->
<div class="row mt-4">    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Productos</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProductos }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-boxes fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Stock Disponible</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stockDisponible }} unidades</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-warehouse fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Stock Bajo</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stockBajo }} productos</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Productos Agotados</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $productosAgotados }} productos</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Ejemplo de código JavaScript para gestionar la interfaz
        console.log('Interfaz de gestión de inventario cargada');
    });
</script>
@endsection
