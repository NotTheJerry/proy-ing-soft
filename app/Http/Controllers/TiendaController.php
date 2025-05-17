<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Inventario;
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
            $query->where('categoria_id', $request->categoria);
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
                $query->orderBy('id_producto', 'desc'); // Por defecto: más nuevos primero
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
    public function carrito()
    {
        $this->requireRole('cliente');

        try {
            // Usar el servicio de carrito para obtener los datos
            $cartService = new \App\Services\CartService();
            $cartData = $cartService->getCart();
            
            $carrito = $cartData['items'];
            $total = $cartData['total'];
            
            $debug_info = [];
            // $debug_info[] = "Número de items en el carrito: " . count($carrito);
            
            // Actualizar información de productos y calcular total
            foreach ($carrito as $key => &$item) {
                try {
                    if (!isset($item['id_producto'])) {
                        $debug_info[] = "Item $key no tiene id_producto";
                        unset($carrito[$key]);
                        continue;
                    }
                    
                    // $debug_info[] = "Procesando producto ID: {$item['id_producto']}";
                    $producto = Producto::with('inventario')->find($item['id_producto']);
                    
                    if (!$producto) {
                        $debug_info[] = "Producto con id {$item['id_producto']} no encontrado";
                        unset($carrito[$key]);
                        continue;
                    }
                    
                    if (!$producto->inventario) {
                        $debug_info[] = "El producto {$producto->descripcion} (ID: {$item['id_producto']}) no tiene inventario asociado";
                        continue;
                    }
                    
                    $item['producto'] = $producto;
                    $item['subtotal'] = $producto->precio * $item['cantidad'];
                    $total += $item['subtotal'];
                    
                    $debug_info[] = "Producto {$producto->descripcion} (ID: {$item['id_producto']}) procesado correctamente. 
                                    Stock: {$producto->inventario->cantidad_disponible}, 
                                    Cantidad: {$item['cantidad']}, 
                                    Subtotal: {$item['subtotal']}";
                    
                } catch (\Exception $e) {
                    $debug_info[] = "Error procesando item $key: " . $e->getMessage();
                    unset($carrito[$key]);
                }
            }
            
            // Reindexar el array si hemos eliminado algún elemento
            if (count($carrito) != count(array_values($carrito))) {
                $carrito = array_values($carrito);
                Session::put('carrito', $carrito);
                $debug_info[] = "El carrito ha sido reindexado después de eliminar items inválidos";
            }
            
            return view('tienda.carrito', compact('carrito', 'total', 'debug_info'));
        } catch (\Exception $e) {
            // En caso de error, mostrar información de depuración
            return response()->view('tienda.error', [
                'mensaje' => 'Error al procesar el carrito: ' . $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]
            ], 500);
        }
    }
    
    /**
     * Agrega un producto al carrito
     */
    public function agregarAlCarrito(Request $request)
    {
        $this->requireRole('cliente');
        
        $request->validate([
            'id_producto' => 'required|exists:productos,id_producto',
            'cantidad' => 'required|integer|min:1'
        ]);
        
        $idProducto = $request->id_producto;
        $cantidad = $request->cantidad;
        
        try {
            // Usar el servicio de carrito para agregar el producto
            $cartService = new \App\Services\CartService();
            $result = $cartService->addToCart($idProducto, $cantidad);
            
            if ($result['success']) {
                return back()->with('success', $result['message']);
            } else {
                return back()->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error al agregar el producto al carrito: ' . $e->getMessage());
        }
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
        
        try {
            $carrito = Session::get('carrito', []);
            $cantidades = $request->cantidades;
            $errores = [];
            $actualizados = [];
            
            foreach ($carrito as $key => &$item) {
                if (isset($cantidades[$item['id_producto']])) {
                    $nuevaCantidad = $cantidades[$item['id_producto']];
                    
                    // Verificar stock
                    $producto = Producto::with('inventario')->find($item['id_producto']);
                    if (!$producto) {
                        $errores[] = "El producto con ID {$item['id_producto']} ya no existe";
                        unset($carrito[$key]);
                        continue;
                    }
                    
                    if (!$producto->inventario) {
                        $errores[] = "El producto {$producto->descripcion} no tiene inventario asociado";
                        continue;
                    }
                    
                    if ($nuevaCantidad <= $producto->inventario->cantidad_disponible) {
                        $item['cantidad'] = $nuevaCantidad;
                        $actualizados[] = $producto->descripcion;
                    } else {
                        $errores[] = "No hay suficiente stock para '{$producto->descripcion}'. Stock disponible: {$producto->inventario->cantidad_disponible}";
                    }
                }
            }
            
            // Reindexar el array si hemos eliminado algún elemento
            if (count($carrito) != count(array_values($carrito))) {
                $carrito = array_values($carrito);
            }
            
            Session::put('carrito', $carrito);
            
            if (count($errores) > 0) {
                // Registramos los éxitos pero mostramos que hubo errores
                Session::flash('warning', implode("<br>", $errores));
                if (count($actualizados) > 0) {
                    Session::flash('info', "Se actualizaron correctamente: " . implode(", ", $actualizados));
                }
                return back();
            }
            
            return back()->with('success', 'Carrito actualizado correctamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar el carrito: ' . $e->getMessage());
        }
    }
    
    /**
     * Elimina un producto del carrito
     */
    public function eliminarDelCarrito($id)
    {
        $this->requireRole('cliente');
        
        try {
            $carrito = Session::get('carrito', []);
            $productoEliminado = null;
            
            foreach ($carrito as $key => $item) {
                if ($item['id_producto'] == $id) {
                    // Opcionalmente, obtener información del producto para mostrar en mensaje
                    $producto = Producto::find($id);
                    if ($producto) {
                        $productoEliminado = $producto->descripcion;
                    }
                    
                    unset($carrito[$key]);
                    break;
                }
            }
            
            // Reindexar el array
            $carrito = array_values($carrito);
            Session::put('carrito', $carrito);
            
            if ($productoEliminado) {
                return back()->with('success', "'{$productoEliminado}' fue eliminado del carrito");
            } else {
                return back()->with('success', 'Producto eliminado del carrito');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el producto del carrito: ' . $e->getMessage());
        }
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
            // Validar el carrito antes de procesarlo
            $errores = [];
            $productosValidados = [];
            $totalValidado = 0;
            
            foreach ($carrito as $key => $item) {
                // Verificar que cada elemento del carrito sea válido
                if (!isset($item['id_producto']) || !isset($item['cantidad'])) {
                    $errores[] = 'Elemento del carrito no válido'; 
                    continue;
                }
                
                $producto = Producto::with('inventario')->find($item['id_producto']);
                
                if (!$producto) {
                    $errores[] = "El producto con ID {$item['id_producto']} ya no existe";
                    continue;
                }
                
                if (!$producto->inventario) {
                    $errores[] = "El producto '{$producto->descripcion}' no tiene inventario asociado";
                    continue;
                }
                
                if ($producto->inventario->cantidad_disponible < $item['cantidad']) {
                    $errores[] = "No hay suficiente stock para '{$producto->descripcion}'. 
                                Solicitado: {$item['cantidad']}, Disponible: {$producto->inventario->cantidad_disponible}";
                    continue;
                }
                
                // El producto es válido para la compra
                $productosValidados[] = [
                    'producto' => $producto,
                    'cantidad' => $item['cantidad'],
                    'precio' => $producto->precio,
                    'subtotal' => $producto->precio * $item['cantidad']
                ];
                
                $totalValidado += $producto->precio * $item['cantidad'];
            }
            
            // Si hay errores, no procesar la compra
            if (count($errores) > 0) {
                DB::rollBack();
                return redirect()->route('tienda.carrito')
                    ->with('error', 'No se pudo procesar la compra debido a los siguientes errores:<br>' . implode('<br>', $errores));
            }

            // Crear la venta
            $venta = new Venta();
            $venta->cliente_id = Auth::id();
            $venta->fecha_venta = now();
            $venta->total_venta = $totalValidado;
            $venta->metodo_pago = 'tarjeta'; // Valor por defecto
            $venta->save();
            
            // Crear los detalles y actualizar el stock
            foreach ($productosValidados as $item) {
                // Crear detalle
                $detalle = new DetalleVenta();
                $detalle->venta_id = $venta->id_venta;
                $detalle->producto_id = $item['producto']->id_producto;
                $detalle->cantidad = $item['cantidad'];
                $detalle->precio_unitario = $item['precio'];
                $detalle->subtotal = $item['subtotal'];
                $detalle->save();
                
                // Actualizar stock en el inventario
                $inventario = $item['producto']->inventario;
                $inventario->cantidad_disponible -= $item['cantidad'];
                $inventario->fecha_ultima_actualizacion = now();
                $inventario->save();
            }
            
            // Confirmar transacción
            DB::commit();
            
            // Limpiar carrito
            Session::forget('carrito');
            
            return redirect()->route('tienda.mis-compras')
                ->with('success', 'Compra realizada exitosamente por un total de $' . number_format($totalValidado, 2));
                
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
        
        $cliente = Auth::user();
        $compras = Venta::where('cliente_id', $cliente->id)
            ->orderBy('fecha_venta', 'desc')
            ->with('detalles.producto')
            ->paginate(10);
            
        // Calcular estadísticas de compras
        $totalCompras = Venta::where('cliente_id', $cliente->id)->count();
        $gastoTotal = Venta::where('cliente_id', $cliente->id)->sum('total_venta');
            
        return view('tienda.mis-compras', compact('compras', 'totalCompras', 'gastoTotal', 'cliente'));
    }
}
