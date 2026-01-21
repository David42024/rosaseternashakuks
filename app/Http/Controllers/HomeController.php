<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::with(['category', 'images'])
            ->active()
            ->featured()
            ->take(8)
            ->get();

        $categories = Category::active()
            ->withCount('products')
            ->get();

        $latestProducts = Product::with(['category', 'images'])
            ->active()
            ->latest()
            ->take(4)
            ->get();

        return Inertia::render('Home', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'latestProducts' => $latestProducts,
        ]);
    }
}