<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartService;
use Illuminate\Support\Facades\Session;

class CarritoController extends Controller
{
    protected $cartService;

    public function __construct()
    {
        $this->cartService = new CartService();
    }

    /**
     * Muestra el carrito de compras
     */
    public function show()
    {
        $this->requireRole('cliente');
        
        try {
            // Obtener datos del carrito usando el servicio
            $cartData = $this->cartService->getCart();
            
            return view('tienda.carrito', [
                'carrito' => $cartData['items'],
                'total' => $cartData['total']
            ]);
        } catch (\Exception $e) {
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
     * Agregar un producto al carrito
     */
    public function add(Request $request)
    {
        $this->requireRole('cliente');
        
        $request->validate([
            'id_producto' => 'required|exists:productos,id_producto',
            'cantidad' => 'required|integer|min:1'
        ]);
        
        try {
            $result = $this->cartService->addToCart(
                $request->id_producto, 
                $request->cantidad
            );
            
            if ($result['success']) {
                return back()->with('success', $result['message']);
            } else {
                return back()->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error al agregar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar cantidad de productos en el carrito
     */
    public function update(Request $request)
    {
        $this->requireRole('cliente');
        
        $request->validate([
            'cantidades' => 'required|array',
            'cantidades.*' => 'integer|min:1'
        ]);
        
        try {
            $cantidades = $request->cantidades;
            $errores = [];
            $actualizados = [];
            
            foreach ($cantidades as $productoId => $cantidad) {
                $result = $this->cartService->updateCartItem($productoId, $cantidad);
                
                if ($result['success']) {
                    $actualizados[] = "Producto actualizado";
                } else {
                    $errores[] = $result['message'];
                }
            }
            
            if (count($errores) > 0) {
                Session::flash('warning', implode("<br>", $errores));
                if (count($actualizados) > 0) {
                    Session::flash('info', "Se actualizaron correctamente: " . count($actualizados) . " productos");
                }
                return back();
            }
            
            return back()->with('success', 'Carrito actualizado correctamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar el carrito: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar un producto del carrito
     */
    public function remove($id)
    {
        $this->requireRole('cliente');
        
        try {
            $result = $this->cartService->removeFromCart($id);
            
            if ($result['success']) {
                return back()->with('success', $result['message']);
            } else {
                return back()->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Vaciar el carrito
     */
    public function clear()
    {
        $this->requireRole('cliente');
        
        try {
            $result = $this->cartService->clearCart();
            
            if ($result['success']) {
                return back()->with('success', 'El carrito ha sido vaciado');
            } else {
                return back()->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error al vaciar el carrito: ' . $e->getMessage());
        }
    }
}
