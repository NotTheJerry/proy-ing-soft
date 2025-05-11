<?php

namespace App\Http\Controllers;

use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TiendaController extends Controller
{
    // Eliminamos el método checkUserRole ya que ahora usamos los métodos del controlador base

    /**
     * Muestra la página principal de la tienda
     */
    public function index(Request $request)
    {
        $query = Producto::where('estado', 'activo')
            ->whereHas('inventario', function($q) {
                $q->where('cantidad_disponible', '>', 0);
            });
        
        // Filtrar por categoría si es necesario
        if ($request->has('categoria') && $request->categoria > 0) {
            $query->where('id_categoria', $request->categoria);
        }
        
        // Ordenar productos
        $orden = $request->get('orden', 'nuevo');
        switch ($orden) {
            case 'precio_asc':
                $query->orderBy('precio', 'asc');
                break;
            case 'precio_desc':
                $query->orderBy('precio', 'desc');
                break;
            default:
                $query->orderBy('id', 'desc'); // Por defecto: más nuevos primero
                break;
        }
        
        $productos = $query->paginate(12);
        $categorias = \App\Models\Categoria::all();
        
        return view('tienda.index', compact('productos', 'categorias', 'orden'));
    }
    
    /**
     * Muestra los detalles de un producto
     */
    public function mostrarProducto($id)
    {
        $producto = Producto::with('inventario')->findOrFail($id);
        
        // Productos relacionados (misma categoría)
        $relacionados = Producto::where('categoria_id', $producto->categoria_id)
            ->where('id_producto', '!=', $producto->id_producto)
            ->where('estado', 'activo')
            ->whereHas('inventario', function($q) {
                $q->where('cantidad_disponible', '>', 0);
            })
            ->limit(4)
            ->get();
            
        return view('tienda.producto', compact('producto', 'relacionados'));
    }
    
    /**
     * Muestra el carrito de compras
     */
    public function verCarrito()
    {
        $this->requireRole('cliente');
        
        $carrito = Session::get('carrito', []);
        $total = 0;
        
        // Actualizar información de productos y calcular total
        foreach ($carrito as &$item) {
            $producto = Producto::with('inventario')->find($item['id']);
            if ($producto) {
                $item['producto'] = $producto;
                $item['subtotal'] = $producto->precio * $item['cantidad'];
                $total += $item['subtotal'];
            }
        }
        
        return view('tienda.carrito', compact('carrito', 'total'));
    }
    
    /**
     * Agrega un producto al carrito
     */
    public function agregarAlCarrito(Request $request)
    {
        $this->requireRole('cliente');
        
        $request->validate([
            'id_producto' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1'
        ]);
        
        $idProducto = $request->id_producto;
        $cantidad = $request->cantidad;
        
        // Verificar stock disponible
        $producto = Producto::with('inventario')->find($idProducto);
        if (!$producto || !$producto->inventario || $producto->inventario->cantidad_disponible < $cantidad) {
            return back()->with('error', 'No hay suficiente stock disponible');
        }
        
        // Obtener el carrito actual
        $carrito = Session::get('carrito', []);
        
        // Verificar si el producto ya está en el carrito
        $encontrado = false;
        foreach ($carrito as &$item) {
            if ($item['id'] == $idProducto) {
                // Verificar que la cantidad total no exceda el stock
                $nuevaCantidad = $item['cantidad'] + $cantidad;
                if ($nuevaCantidad > $producto->cantidad) {
                    return back()->with('error', 'No hay suficiente stock disponible');
                }
                
                $item['cantidad'] = $nuevaCantidad;
                $encontrado = true;
                break;
            }
        }
        
        // Si no está en el carrito, agregarlo
        if (!$encontrado) {
            $carrito[] = [
                'id' => $idProducto,
                'cantidad' => $cantidad,
                'precio' => $producto->precio
            ];
        }
        
        Session::put('carrito', $carrito);
        return back()->with('success', 'Producto agregado al carrito');
    }
    
    /**
     * Actualiza las cantidades en el carrito
     */
    public function actualizarCarrito(Request $request)
    {
        $this->requireRole('cliente');
        
        $request->validate([
            'cantidades' => 'required|array',
            'cantidades.*' => 'integer|min:1'
        ]);
        
        $carrito = Session::get('carrito', []);
        $cantidades = $request->cantidades;
        
        foreach ($carrito as $key => &$item) {
            if (isset($cantidades[$item['id']])) {
                $nuevaCantidad = $cantidades[$item['id']];
                
                // Verificar stock
                $producto = Producto::with('inventario')->find($item['id']);
                if ($producto && $producto->inventario && $nuevaCantidad <= $producto->inventario->cantidad_disponible) {
                    $item['cantidad'] = $nuevaCantidad;
                } else {
                    return back()->with('error', 'No hay suficiente stock para el producto ' . $producto->descripcion);
                }
            }
        }
        
        Session::put('carrito', $carrito);
        return back()->with('success', 'Carrito actualizado correctamente');
    }
    
    /**
     * Elimina un producto del carrito
     */
    public function eliminarDelCarrito($id)
    {
        $this->requireRole('cliente');
        
        $carrito = Session::get('carrito', []);
        
        foreach ($carrito as $key => $item) {
            if ($item['id'] == $id) {
                unset($carrito[$key]);
                break;
            }
        }
        
        // Reindexar el array
        $carrito = array_values($carrito);
        Session::put('carrito', $carrito);
        
        return back()->with('success', 'Producto eliminado del carrito');
    }
    
    /**
     * Procesa la compra
     */
    public function procesarCompra()
    {
        $this->requireRole('cliente');
        
        $carrito = Session::get('carrito', []);
        
        if (empty($carrito)) {
            return redirect()->route('tienda.carrito')->with('error', 'El carrito está vacío');
        }
        
        // Iniciar transacción
        DB::beginTransaction();
        try {
            // Crear la venta
            $venta = new Venta();
            $venta->id_cliente = Auth::id();
            $venta->fecha = now();
            $venta->total = 0; // Se actualizará luego
            $venta->save();
            
            $total = 0;
            
            // Crear los detalles y actualizar el stock
            foreach ($carrito as $item) {
                $producto = Producto::with('inventario')->find($item['id']);
                
                if (!$producto || !$producto->inventario || $producto->inventario->cantidad_disponible < $item['cantidad']) {
                    DB::rollBack();
                    return redirect()->route('tienda.carrito')
                        ->with('error', 'No hay suficiente stock para ' . ($producto ? $producto->descripcion : 'un producto'));
                }
                
                // Crear detalle
                $detalle = new DetalleVenta();
                $detalle->id_venta = $venta->id;
                $detalle->id_producto = $producto->id;
                $detalle->cantidad = $item['cantidad'];
                $detalle->precio_unitario = $producto->precio;
                $detalle->subtotal = $producto->precio * $item['cantidad'];
                $detalle->save();
                
                // Actualizar stock en el inventario
                $inventario = $producto->inventario;
                $inventario->cantidad_disponible -= $item['cantidad'];
                $inventario->fecha_ultima_actualizacion = now();
                $inventario->save();
                
                $total += $detalle->subtotal;
            }
            
            // Actualizar el total de la venta
            $venta->total = $total;
            $venta->save();
            
            // Confirmar transacción
            DB::commit();
            
            // Limpiar carrito
            Session::forget('carrito');
            
            return redirect()->route('tienda.mis-compras')
                ->with('success', 'Compra realizada exitosamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('tienda.carrito')
                ->with('error', 'Ocurrió un error al procesar la compra: ' . $e->getMessage());
        }
    }
    
    /**
     * Muestra el historial de compras del cliente
     */
    public function misCompras()
    {
        $this->requireRole('cliente');
        
        $compras = Venta::where('id_cliente', Auth::id())
            ->orderBy('fecha', 'desc')
            ->with('detalles.producto')
            ->paginate(10);
            
        return view('tienda.mis-compras', compact('compras'));
    }
}
