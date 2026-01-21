<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// ==========================================
// RUTAS PÚBLICAS
// ==========================================

Route::get('/', [HomeController::class, 'index'])->name('home');

// Catálogo
Route::get('/catalogo', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalogo/{slug}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/categoria/{slug}', [CatalogController::class, 'category'])->name('catalog.category');

// Carrito
Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
Route::post('/carrito/agregar', [CartController::class, 'add'])->name('cart.add');
Route::patch('/carrito/actualizar/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/carrito/eliminar/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/carrito/vaciar', [CartController::class, 'clear'])->name('cart.clear');

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/whatsapp', [CheckoutController::class, 'whatsapp'])->name('checkout.whatsapp');
Route::get('/pedido/confirmacion/{order}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

// ==========================================
// RUTAS DE AUTENTICACIÓN (Breeze)
// ==========================================

require __DIR__.'/auth.php';

// ==========================================
// RUTAS DE ADMIN (protegidas)
// ==========================================

Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Categorías
    Route::resource('categories', CategoryController::class);
    
    // Productos
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/images', [ProductController::class, 'uploadImage'])->name('products.images.upload');
    Route::delete('products/images/{image}', [ProductController::class, 'deleteImage'])->name('products.images.delete');
    
    // Pedidos
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
});