<?php

namespace App\Services;

use App\Models\Producto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Obtener todos los items del carrito actual
     * 
     * @return array
     */
    public function getCart()
    {
        // Inicializar carrito si no existe
        if (!Session::has('carrito')) {
            Session::put('carrito', []);
        }
        
        $carrito = Session::get('carrito');
        $items = [];
        $total = 0;
        
        foreach ($carrito as $id => $cantidad) {
            $producto = Producto::with('categoria', 'proveedor', 'inventario')->find($id);
            
            if ($producto) {
                $subtotal = $producto->precio * $cantidad;
                $items[] = [
                    'id_producto' => $id,
                    'producto' => $producto,
                    'cantidad' => $cantidad,
                    'subtotal' => $subtotal
                ];
                $total += $subtotal;
            }
        }
        
        return [
            'items' => $items,
            'total' => $total
        ];
    }
    
    /**
     * Agregar un producto al carrito
     * 
     * @param int $productoId
     * @param int $cantidad
     * @return array
     */
    public function addToCart($productoId, $cantidad)
    {
        // Verificar que el usuario esté autenticado con el rol de cliente
        if (!Auth::check() || Auth::user()->role !== 'cliente') {
            return [
                'success' => false, 
                'message' => 'Debes iniciar sesión como cliente para agregar productos al carrito'
            ];
        }
        
        // Buscar el producto y verificar que exista
        $producto = Producto::with('inventario')->find($productoId);
        
        if (!$producto) {
            return [
                'success' => false, 
                'message' => 'El producto no existe'
            ];
        }
        
        // Verificar si tiene inventario disponible
        if (!$producto->inventario || $producto->inventario->cantidad_disponible < 1) {
            return [
                'success' => false, 
                'message' => 'El producto no tiene stock disponible'
            ];
        }
        
        // Verificar que la cantidad solicitada esté disponible
        if ($producto->inventario->cantidad_disponible < $cantidad) {
            return [
                'success' => false, 
                'message' => "Solo hay {$producto->inventario->cantidad_disponible} unidades disponibles de este producto"
            ];
        }
        
        // Inicializar carrito si no existe
        if (!Session::has('carrito')) {
            Session::put('carrito', []);
        }
        
        $carrito = Session::get('carrito');
        
        // Actualizar cantidad si el producto ya está en el carrito
        if (isset($carrito[$productoId])) {
            $nuevaCantidad = $carrito[$productoId] + $cantidad;
            
            // Verificar nuevamente si hay suficiente stock
            if ($nuevaCantidad > $producto->inventario->cantidad_disponible) {
                return [
                    'success' => false, 
                    'message' => "No puedes agregar más unidades. Stock disponible: {$producto->inventario->cantidad_disponible}"
                ];
            }
            
            $carrito[$productoId] = $nuevaCantidad;
        } else {
            // Agregar nuevo producto al carrito
            $carrito[$productoId] = $cantidad;
        }
        
        Session::put('carrito', $carrito);
        
        return [
            'success' => true, 
            'message' => "Se agregó {$cantidad} unidad(es) de {$producto->descripcion} al carrito",
            'cart_count' => count($carrito)
        ];
    }
    
    /**
     * Actualizar la cantidad de un producto en el carrito
     * 
     * @param int $productoId
     * @param int $cantidad
     * @return array
     */
    public function updateCartItem($productoId, $cantidad)
    {
        if (!Session::has('carrito') || !isset(Session::get('carrito')[$productoId])) {
            return [
                'success' => false, 
                'message' => 'El producto no está en el carrito'
            ];
        }
        
        $producto = Producto::with('inventario')->find($productoId);
        
        if (!$producto) {
            return [
                'success' => false, 
                'message' => 'El producto no existe'
            ];
        }
        
        // Verificar stock disponible
        if ($cantidad > $producto->inventario->cantidad_disponible) {
            return [
                'success' => false, 
                'message' => "Solo hay {$producto->inventario->cantidad_disponible} unidades disponibles"
            ];
        }
        
        $carrito = Session::get('carrito');
        $carrito[$productoId] = $cantidad;
        Session::put('carrito', $carrito);
        
        return [
            'success' => true, 
            'message' => 'Carrito actualizado correctamente',
            'item_subtotal' => $cantidad * $producto->precio
        ];
    }
    
    /**
     * Eliminar un producto del carrito
     * 
     * @param int $productoId
     * @return array
     */
    public function removeFromCart($productoId)
    {
        if (!Session::has('carrito') || !isset(Session::get('carrito')[$productoId])) {
            return [
                'success' => false, 
                'message' => 'El producto no está en el carrito'
            ];
        }
        
        $carrito = Session::get('carrito');
        $producto = Producto::find($productoId);
        $nombre = $producto ? $producto->descripcion : 'El producto';
        
        unset($carrito[$productoId]);
        Session::put('carrito', $carrito);
        
        return [
            'success' => true, 
            'message' => "{$nombre} ha sido eliminado del carrito",
            'cart_count' => count($carrito)
        ];
    }
    
    /**
     * Vaciar el carrito por completo
     * 
     * @return array
     */
    public function clearCart()
    {
        Session::put('carrito', []);
        
        return [
            'success' => true, 
            'message' => 'El carrito ha sido vaciado'
        ];
    }
}
