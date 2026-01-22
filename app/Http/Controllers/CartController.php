<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $items = [];
        $subtotal = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::with('images')->find($productId);
            if ($product) {
                $itemTotal = $product->current_price * $item['quantity'];
                $items[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->current_price,
                    'original_price' => $product->price,
                    'image' => $product->primary_image_url,
                    'quantity' => $item['quantity'],
                    'total' => $itemTotal,
                    'max_stock' => $product->stock,
                ];
                $subtotal += $itemTotal;
            }
        }

        return Inertia::render('Cart/Index', [
            'items' => $items,
            'subtotal' => $subtotal,
            'total' => $subtotal, // Puedes agregar envío, descuentos, etc.
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1|max:10',
        ]);

        $productId = $request->product_id;
        $quantity = $request->get('quantity', 1);

        $product = Product::active()->findOrFail($productId);

        $cart = $request->session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'quantity' => $quantity,
            ];
        }

        $request->session()->put('cart', $cart);

        return back()->with('success', "{$product->name} agregado al carrito");
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $cart = $request->session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $request->quantity;
            $request->session()->put('cart', $cart);
        }

        return back()->with('success', 'Carrito actualizado');
    }

    public function remove(Request $request, int $id)
    {
        $cart = $request->session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            $request->session()->put('cart', $cart);
        }

        return back()->with('success', 'Producto eliminado del carrito');
    }

    public function clear(Request $request)
    {
        $request->session()->forget('cart');

        return back()->with('success', 'Carrito vaciado');
    }
}