<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user');

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('customer_name', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_phone', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'statuses' => Order::getStatuses(),
            'filters' => $request->only(['status', 'search', 'date_from', 'date_to']),
        ]);
    }

    public function create()
    {
        $products = Product::with('images')
            ->active()
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Orders/Create', [
            'products' => $products,
            'statuses' => Order::getStatuses(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:' . implode(',', array_keys(Order::getStatuses())),
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        // Calcular totales
        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $order = Order::create([
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_email' => $validated['customer_email'] ?? null,
            'address' => $validated['address'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => $validated['status'],
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ]);

        // Crear items
        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            
            $order->items()->create([
                'product_id' => $item['product_id'],
                'product_name' => $product->name,
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['price'] * $item['quantity'],
            ]);
        }

        return redirect()->route('admin.orders.index')
            ->with('success', 'Pedido creado correctamente');
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product.images']);

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
            'statuses' => Order::getStatuses(),
        ]);
    }

    public function edit(Order $order)
    {
        $order->load('items');
        
        $products = Product::with('images')
            ->active()
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Orders/Edit', [
            'order' => $order,
            'products' => $products,
            'statuses' => Order::getStatuses(),
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:' . implode(',', array_keys(Order::getStatuses())),
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        // Calcular totales
        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $order->update([
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_email' => $validated['customer_email'] ?? null,
            'address' => $validated['address'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => $validated['status'],
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ]);

        // Eliminar items anteriores y crear nuevos
        $order->items()->delete();

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            
            $order->items()->create([
                'product_id' => $item['product_id'],
                'product_name' => $product->name,
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['price'] * $item['quantity'],
            ]);
        }

        return redirect()->route('admin.orders.index')
            ->with('success', 'Pedido actualizado correctamente');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Order::getStatuses())),
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Estado actualizado correctamente');
    }

    public function destroy(Order $order)
    {
        $order->items()->delete();
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Pedido eliminado correctamente');
    }
}