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
    public function generarReporte()
    {
        $inventarios = \App\Models\Inventario::with(['producto.categoria', 'producto.proveedor'])->get();
        
        // Aquí idealmente generaríamos un PDF o Excel
        
        // Por ahora simplemente creamos un registro en la tabla reportes
        \App\Models\Reporte::create([
            'tipo_reporte' => 'Inventario',
            'fecha_generacion' => now(),
            'contenido' => 'Reporte de inventario generado el ' . now()->format('Y-m-d H:i:s'),
        ]);
        
        return redirect()->back()->with('success', 'Reporte generado correctamente');
    }
}
