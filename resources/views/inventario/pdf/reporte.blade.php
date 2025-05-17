<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte de Inventario</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
        }
        h1 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
        }
        .header {
            width: 100%;
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 120px;
        }
        .date {
            text-align: right;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        table th {
            background-color: #f2f2f2;
            text-align: left;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
        }
        .category-header {
            margin-top: 20px;
            background-color: #e9ecef;
            padding: 5px;
            font-weight: bold;
        }
        .summary {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }
        .summary h3 {
            margin-top: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE INVENTARIO</h1>
    </div>
    
    <div class="date">
        <p>Fecha: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
    
    <div class="summary">
        <h3>Resumen del Inventario</h3>
        <p><strong>Total de Productos:</strong> {{ $totalProductos }}</p>
        <p><strong>Stock Total Disponible:</strong> {{ $stockDisponible }} unidades</p>
        <p><strong>Productos con Stock Bajo:</strong> {{ $stockBajo }}</p>
        <p><strong>Productos Agotados:</strong> {{ $productosAgotados }}</p>
    </div>
    
    @if($inventariosPorCategoria->count() > 0)
        @foreach($inventariosPorCategoria as $categoria => $productos)
            <div class="category-header">
                Categoría: {{ $categoria }}
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Proveedor</th>
                        <th>Stock</th>
                        <th>Última Actualización</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productos as $inventario)
                        <tr>
                            <td>{{ $inventario->producto->id_producto }}</td>
                            <td>{{ $inventario->producto->descripcion }}</td>
                            <td>{{ $inventario->producto->proveedor ? $inventario->producto->proveedor->nombre : 'Sin proveedor' }}</td>
                            <td style="{{ $inventario->cantidad_disponible < 10 ? 'color:red;font-weight:bold;' : '' }}">
                                {{ $inventario->cantidad_disponible }}
                            </td>
                            <td>{{ $inventario->fecha_ultima_actualizacion->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    @else
        <p>No hay productos en el inventario.</p>
    @endif
    
    <div class="footer">
        <p>Este reporte fue generado automáticamente por el Sistema de Gestión de Inventario.</p>
        <p>© {{ date('Y') }} - Empresa de Gestión de Inventario</p>
    </div>
</body>
</html>
