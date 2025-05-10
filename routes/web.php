<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::get('/', function () {
    return redirect()->route('inventario.index');
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

// Rutas para la gestión de inventario (protegidas)
Route::middleware('auth')->group(function () {
    Route::controller(\App\Http\Controllers\InventarioController::class)->prefix('inventario')->group(function () {
        Route::get('/', 'index')->name('inventario.index');
        Route::get('/stock-bajo', 'stockBajo')->name('inventario.stock-bajo');
        Route::post('/filtrar', 'filtrarPorCategoria')->name('inventario.filtrar');
        Route::patch('/actualizar/{id}', 'actualizarCantidad')->name('inventario.actualizar');
        Route::get('/reporte', 'generarReporte')->name('inventario.reporte');
    });
});
