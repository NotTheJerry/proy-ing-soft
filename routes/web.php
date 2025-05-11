<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        // Si el usuario está autenticado, redirigir según el rol
        if (Auth::user()->role === 'admin') {
            return redirect()->route('inventario.index');
        } else {
            return redirect()->route('tienda.index');
        }
    }
    // Si no está autenticado, redirigir al login
    return redirect()->route('login');
});

// Rutas de autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    // Rutas para restablecimiento de contraseña
    Route::get('/password/reset', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [AuthController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas para el perfil de usuario (protegidas)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [AuthController::class, 'showProfileForm'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
});

// Redireccionamiento basado en rol después del login
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        if (Auth::user() && Auth::user()->role === 'admin') {
            return redirect()->route('inventario.index');
        } else {
            return redirect()->route('tienda.index');
        }
    })->name('dashboard');
});

// Rutas públicas
Route::controller(\App\Http\Controllers\TiendaController::class)->prefix('tienda')->group(function () {
    Route::get('/', 'index')->name('tienda.index');
    Route::get('/producto/{id}', 'mostrarProducto')->name('tienda.producto');
});

// Rutas protegidas (requieren autenticación)
Route::middleware('auth')->group(function () {
    // Rutas de inventario (solo administradores)
    Route::prefix('inventario')->middleware('role:admin')->group(function () {
            Route::controller(\App\Http\Controllers\InventarioController::class)->group(function () {
                Route::get('/', 'index')->name('inventario.index');
                Route::get('/stock-bajo', 'stockBajo')->name('inventario.stock-bajo');
                Route::post('/filtrar', 'filtrarPorCategoria')->name('inventario.filtrar');
                Route::patch('/actualizar/{id}', 'actualizarCantidad')->name('inventario.actualizar');
                Route::get('/reporte', 'generarReporte')->name('inventario.reporte');
            });
    });

    // Rutas de tienda que requieren autenticación y rol de cliente
    Route::prefix('tienda')->middleware('role:cliente')->group(function () {
            Route::controller(\App\Http\Controllers\TiendaController::class)->group(function () {
                Route::get('/carrito', 'carrito')->name('tienda.carrito');
                Route::post('/carrito/agregar', 'agregarAlCarrito')->name('tienda.carrito.agregar');
                Route::post('/carrito/actualizar', 'actualizarCarrito')->name('tienda.carrito.actualizar');
                Route::delete('/carrito/eliminar/{id}', 'eliminarDelCarrito')->name('tienda.carrito.eliminar');
                Route::post('/comprar', 'procesarCompra')->name('tienda.comprar');
                Route::get('/mis-compras', 'misCompras')->name('tienda.mis-compras');
            });
    });
});