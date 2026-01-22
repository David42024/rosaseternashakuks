<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Cache::remember('featured_products', 3600, function () {
            return Product::with(['category', 'images'])
                ->active()
                ->featured()
                ->take(8)
                ->get();
        });

        $categories = Cache::remember('categories', 3600, function () {
            return Category::active()->withCount('products')->get();
        });

        $latestProducts = Cache::remember('latest_products', 3600, function () {
            return Product::with(['category', 'images'])
                ->active()
                ->latest()
                ->take(4)
                ->get();
        });

        return Inertia::render('Home', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'latestProducts' => $latestProducts,
        ]);
    }
}