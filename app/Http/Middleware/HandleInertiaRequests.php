<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            
            // Usuario autenticado
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'role' => $request->user()->role,
                    'isAdmin' => $request->user()->isAdmin(),
                ] : null,
            ],
            
            // Flash messages
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            
            // Carrito (cantidad de items)
            'cartCount' => fn () => $this->getCartCount($request),
        ];
    }

    /**
     * Obtener cantidad de items en el carrito
     */
    private function getCartCount(Request $request): int
    {
        $cart = $request->session()->get('cart', []);
        return array_sum(array_column($cart, 'quantity'));
    }
}