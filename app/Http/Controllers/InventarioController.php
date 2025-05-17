<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventarioController extends Controller
{
    /**
     * Mostrar la vista principal del inventario
     */
    public function index()
    {
        $this->requireRole('admin');
        
        $inventarios = \App\Models\Inventario::with(['producto.categoria', 'producto.proveedor'])->paginate(10);
        $categorias = \App\Models\Categoria::all();
        
        // Calcular datos de resumen para el dashboard
        $totalProductos = \App\Models\Inventario::count();
        $stockDisponible = \App\Models\Inventario::sum('cantidad_disponible');
        $stockBajo = \App\Models\Inventario::where('cantidad_disponible', '<', 10)
            ->where('cantidad_disponible', '>', 0)
            ->count();
        $productosAgotados = \App\Models\Inventario::where('cantidad_disponible', '<=', 0)->count();
        
        return view('inventario.index', compact(
            'inventarios', 
            'categorias', 
            'totalProductos', 
            'stockDisponible', 
            'stockBajo',
            'productosAgotados'
        ));
    }
    
    /**
     * Filtrar productos por categoría
     */
    public function filtrarPorCategoria(Request $request)
    {
        $this->requireRole('admin');
        
        $categoria_id = $request->categoria_id;
        $stock = $request->stock;
        
        $query = \App\Models\Inventario::with(['producto.categoria', 'producto.proveedor']);
        
        if ($categoria_id) {
            $query->whereHas('producto', function ($q) use ($categoria_id) {
                $q->where('categoria_id', $categoria_id);
            });
        }
        
        if ($stock) {
            switch ($stock) {
                case 'disponible':
                    $query->where('cantidad_disponible', '>', 10);
                    break;
                case 'bajo':
                    $query->where('cantidad_disponible', '>', 0)->where('cantidad_disponible', '<', 10);
                    break;
                case 'agotado':
                    $query->where('cantidad_disponible', '<=', 0);
                    break;
            }
        }
        
        $inventarios = $query->paginate(10);
        $categorias = \App\Models\Categoria::all();
        
        // Calcular datos de resumen para el dashboard
        $totalProductos = \App\Models\Inventario::count();
        $stockDisponible = \App\Models\Inventario::sum('cantidad_disponible');
        $stockBajo = \App\Models\Inventario::where('cantidad_disponible', '<', 10)
            ->where('cantidad_disponible', '>', 0)
            ->count();
        $productosAgotados = \App\Models\Inventario::where('cantidad_disponible', '<=', 0)->count();
        
        return view('inventario.index', compact(
            'inventarios', 
            'categorias', 
            'totalProductos', 
            'stockDisponible', 
            'stockBajo',
            'productosAgotados'
        ));
    }
    
    /**
     * Actualizar la cantidad de un producto en el inventario
     */
    public function actualizarCantidad(Request $request, $id)
    {
        $this->requireRole('admin');
        
        $request->validate([
            'cantidad_disponible' => 'required|numeric|min:0',
        ]);
        
        $inventario = \App\Models\Inventario::findOrFail($id);
        $inventario->cantidad_disponible = $request->cantidad_disponible;
        $inventario->fecha_ultima_actualizacion = now();
        $inventario->save();
        
        return redirect()->back()->with('success', 'Cantidad actualizada correctamente');
    }
    
    /**
     * Ver los productos con stock bajo
     */
    public function stockBajo()
    {
        $this->requireRole('admin');
        
        // Asumiendo que un stock bajo es menos de 10 unidades
        $inventarios = \App\Models\Inventario::with(['producto.categoria', 'producto.proveedor'])
            ->where('cantidad_disponible', '<', 10)
            ->paginate(10);
        $categorias = \App\Models\Categoria::all();
        
        return view('inventario.stock_bajo', compact('inventarios', 'categorias'));
    }
    
    /**
     * Generar reporte de inventario
     */
    public function generarReporte(Request $request)
    {
        $this->requireRole('admin');
        
        // Obtener todos los inventarios con sus relaciones
        $inventarios = \App\Models\Inventario::with(['producto.categoria', 'producto.proveedor'])->get();
        
        // Calcular datos de resumen para el reporte
        $totalProductos = \App\Models\Inventario::count();
        $stockDisponible = \App\Models\Inventario::sum('cantidad_disponible');
        $stockBajo = \App\Models\Inventario::where('cantidad_disponible', '<', 10)
            ->where('cantidad_disponible', '>', 0)
            ->count();
        $productosAgotados = \App\Models\Inventario::where('cantidad_disponible', '<=', 0)->count();
        
        // Agrupar productos por categoría para el reporte PDF
        $inventariosPorCategoria = $inventarios->groupBy(function ($item) {
            return $item->producto->categoria ? $item->producto->categoria->nombre : 'Sin categoría';
        });
        
        // Registrar la generación del reporte
        $reporte = \App\Models\Reporte::create([
            'tipo_reporte' => 'Inventario',
            'fecha_generacion' => now(),
            'contenido' => 'Reporte de inventario generado el ' . now()->format('Y-m-d H:i:s'),
        ]);
        
        // Determinar el formato del reporte (pdf o excel)
        $formato = $request->input('formato', 'pdf');
        
        if ($formato === 'excel') {
            try {
                // Generar reporte en Excel
                return app(\App\Exports\InventarioExport::class)->export();
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Error al generar el reporte Excel: ' . $e->getMessage());
            }
        } else {
            try {
                // Generar reporte en PDF usando el servicio dedicado
                $pdfService = new \App\Services\PDFService();
                $data = compact(
                    'inventariosPorCategoria',
                    'totalProductos',
                    'stockDisponible',
                    'stockBajo',
                    'productosAgotados'
                );
                return $pdfService->downloadPDF(
                    'inventario.pdf.reporte', 
                    $data, 
                    'reporte_inventario_' . now()->format('Y-m-d') . '.pdf'
                );
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Error al generar el reporte PDF: ' . $e->getMessage());
            }
        }
    }
}
