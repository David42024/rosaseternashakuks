<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;


class CatalogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->hasAny(['search', 'category', 'min_price', 'max_price', 'sort'])) {
            $products = $this->getFilteredProducts($request);
        } else {
            // Sin filtros, usar caché
            $products = Cache::remember('catalog_products_page_' . $request->get('page', 1), 1800, function () {
                return Product::with(['category', 'images'])
                    ->active()
                    ->latest()
                    ->paginate(12);
            });
        }

        $categories = Cache::remember('categories_with_count', 3600, function () {
            return Category::active()->withCount('products')->get();
        });

        return Inertia::render('Catalog/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['category', 'search', 'min_price', 'max_price', 'sort']),
        ]);
    }

    private function getFilteredProducts(Request $request)
    {
        $query = Product::with(['category', 'images'])->active();

        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        switch ($request->get('sort', 'latest')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
        }

        return $query->paginate(12)->withQueryString();
    }

    public function show(string $slug)
    {
        $product = Product::with(['category', 'images'])
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        $relatedProducts = Product::with(['category', 'images'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->take(4)
            ->get();

        return Inertia::render('Catalog/Show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }

    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)->active()->firstOrFail();

        $products = Product::with(['category', 'images'])
            ->where('category_id', $category->id)
            ->active()
            ->paginate(12);

        $categories = Category::active()->withCount('products')->get();

        return Inertia::render('Catalog/Index', [
            'products' => $products,
            'categories' => $categories,
            'currentCategory' => $category,
            'filters' => ['category' => $slug],
        ]);
    }
}