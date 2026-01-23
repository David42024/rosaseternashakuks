<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío');
        }

        $items = [];
        $subtotal = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::with('images')->find($productId);
            if ($product) {
                $itemTotal = $product->current_price * $item['quantity'];
                $items[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->current_price,
                    'image' => $product->primary_image_url,
                    'quantity' => $item['quantity'],
                    'total' => $itemTotal,
                ];
                $subtotal += $itemTotal;
            }
        }

        return Inertia::render('Checkout/Index', [
            'items' => $items,
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
        ]);

        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return back()->with('error', 'Tu carrito está vacío');
        }

        try {
            DB::beginTransaction();

            $subtotal = 0;
            $orderItems = [];

            foreach ($cart as $productId => $item) {
                $product = Product::findOrFail($productId);
                $price = $product->current_price;
                $itemSubtotal = $price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                ];
            }

            $order = Order::create([
                'user_id' => auth()->id(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'address' => $request->address,
                'notes' => $request->notes,
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'status' => Order::STATUS_PENDIENTE,
            ]);

            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            DB::commit();

            // Limpiar carrito
            $request->session()->forget('cart');

            return redirect()->route('checkout.confirmation', $order)
                ->with('success', '¡Pedido realizado con éxito!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar el pedido. Intenta nuevamente.');
        }
    }

    public function whatsapp(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío');
        }

        $message = "🌹 *Nuevo Pedido - Rosas Eternas Hakuks*\n\n";
        $total = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product) {
                $itemTotal = $product->current_price * $item['quantity'];
                $message .= "• {$product->name} x{$item['quantity']} - S/{$itemTotal}\n";
                $total += $itemTotal;
            }
        }

        $message .= "\n*Total: S/{$total}*\n\n";
        $message .= "¡Hola! Me gustaría hacer este pedido. 😊";

        // Número de WhatsApp (configurar en .env)
        $phone = config('app.whatsapp_number', '5218673160224');

        $whatsappUrl = "https://wa.me/{$phone}?text=" . urlencode($message);

        return Inertia::location($whatsappUrl);
    }

    public function confirmation(Order $order)
    {
        $order->load('items');

        return Inertia::render('Checkout/Confirmation', [
            'order' => $order,
        ]);
    }
}