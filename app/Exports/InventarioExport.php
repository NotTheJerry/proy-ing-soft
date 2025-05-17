<?php

namespace App\Exports;

use App\Models\Inventario;
use App\Models\Categoria;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class InventarioExport
{
    public function export()
    {
        return Excel::create('Reporte_Inventario_'.date('d-m-Y'), function($excel) {
            // Primera hoja con resumen
            $excel->sheet('Resumen', function($sheet) {
                $totalProductos = Inventario::count();
                $stockDisponible = Inventario::sum('cantidad_disponible');
                $stockBajo = Inventario::where('cantidad_disponible', '<', 10)
                    ->where('cantidad_disponible', '>', 0)
                    ->count();
                $productosAgotados = Inventario::where('cantidad_disponible', '<=', 0)->count();
                
                $data = [
                    ['RESUMEN DEL INVENTARIO', ''],
                    ['Fecha de generación:', now()->format('d/m/Y H:i:s')],
                    [''],
                    ['Total de productos:', $totalProductos],
                    ['Stock total disponible:', $stockDisponible . ' unidades'],
                    ['Productos con stock bajo:', $stockBajo],
                    ['Productos agotados:', $productosAgotados],
                ];

                $sheet->fromArray($data, null, 'A1', false, false);
                $sheet->mergeCells('A1:B1');
                $sheet->cell('A1', function($cell) {
                    $cell->setFont(['size' => '16', 'bold' => true]);
                });
            });
            
            // Segunda hoja con todos los productos
            $excel->sheet('Inventario Completo', function($sheet) {
                $inventarios = Inventario::with(['producto.categoria', 'producto.proveedor'])
                    ->orderBy('cantidad_disponible', 'asc')
                    ->get();
                
                // Cabeceras
                $sheet->row(1, ['ID', 'Producto', 'Categoría', 'Proveedor', 'Stock', 'Última Actualización']);
                
                // Estilo para las cabeceras
                $sheet->row(1, function($row) {
                    $row->setFont(['bold' => true]);
                    $row->setBackground('#CCCCCC');
                });
                
                $row = 2;
                foreach ($inventarios as $inventario) {
                    $sheet->row($row, [
                        $inventario->producto->id_producto,
                        $inventario->producto->descripcion,
                        $inventario->producto->categoria ? $inventario->producto->categoria->nombre : 'Sin categoría',
                        $inventario->producto->proveedor ? $inventario->producto->proveedor->nombre : 'Sin proveedor',
                        $inventario->cantidad_disponible,
                        $inventario->fecha_ultima_actualizacion->format('d/m/Y'),
                    ]);
                    
                    // Colorear en rojo los productos con stock bajo
                    if ($inventario->cantidad_disponible < 10) {
                        $sheet->cell('E'.$row, function($cell) {
                            $cell->setFont(['color' => ['rgb' => 'FF0000']]);
                            $cell->setFontWeight('bold');
                        });
                    }
                    
                    $row++;
                }
                
                // Auto ajustar el ancho de las columnas
                $sheet->setAutoSize(true);
            });
            
            // Una hoja por cada categoría
            $categorias = Categoria::all();
            foreach ($categorias as $categoria) {
                $excel->sheet('Categoría: '.$categoria->nombre, function($sheet) use ($categoria) {
                    $inventarios = Inventario::with(['producto.categoria', 'producto.proveedor'])
                        ->whereHas('producto', function($query) use ($categoria) {
                            $query->where('categoria_id', $categoria->id_categoria);
                        })
                        ->get();
                    
                    // Cabeceras
                    $sheet->row(1, ['ID', 'Producto', 'Proveedor', 'Stock', 'Última Actualización']);
                    
                    // Estilo para las cabeceras
                    $sheet->row(1, function($row) {
                        $row->setFont(['bold' => true]);
                        $row->setBackground('#CCCCCC');
                    });
                    
                    $row = 2;
                    foreach ($inventarios as $inventario) {
                        $sheet->row($row, [
                            $inventario->producto->id_producto,
                            $inventario->producto->descripcion,
                            $inventario->producto->proveedor ? $inventario->producto->proveedor->nombre : 'Sin proveedor',
                            $inventario->cantidad_disponible,
                            $inventario->fecha_ultima_actualizacion->format('d/m/Y'),
                        ]);
                        
                        // Colorear en rojo los productos con stock bajo
                        if ($inventario->cantidad_disponible < 10) {
                            $sheet->cell('D'.$row, function($cell) {
                                $cell->setFont(['color' => ['rgb' => 'FF0000']]);
                                $cell->setFontWeight('bold');
                            });
                        }
                        
                        $row++;
                    }
                    
                    // Auto ajustar el ancho de las columnas
                    $sheet->setAutoSize(true);
                });
            }
        })->export('xlsx');
    }
}
