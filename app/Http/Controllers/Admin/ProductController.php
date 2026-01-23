<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(Request $request)
    {

        if (!$request->has('status')) {
            return redirect()->route('admin.products.index', ['status' => 'active']);
        }
        
        $query = Product::with(['category', 'images']);

        // Filtros
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $products = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::active()->get();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category', 'status']),
        ]);
    }

    public function create()
    {
        $categories = Category::active()->get();

        return Inertia::render('Admin/Products/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'integer|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . uniqid();

        $product = Product::create($validated);

        // Subir imágenes
        if ($request->hasFile('images')) {
            $baseName = Str::slug($product->name);
            
            foreach ($request->file('images') as $index => $image) {
                $fileName = $baseName . '-' . ($index + 1);
                
                // Subir a Cloudinary
                $upload = (new \Cloudinary\Api\Upload\UploadApi())->upload($image->getRealPath(), [
                    'public_id' => $fileName,
                ]);
                
                $product->images()->create([
                    'image_path' => $fileName,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Producto creado correctamente');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'images']);

        return Inertia::render('Admin/Products/Show', [
            'product' => $product,
        ]);
    }

    public function edit(Product $product)
    {
        $product->load('images');
        $categories = Category::active()->get();

        return Inertia::render('Admin/Products/Edit', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'integer|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $product->update($validated);

        // Subir nuevas imágenes si existen
        if ($request->hasFile('images')) {
            $baseName = Str::slug($product->name);
            $existingCount = $product->images()->count();
            
            foreach ($request->file('images') as $index => $image) {
                $fileName = $baseName . '-' . ($existingCount + $index + 1);
                
                // Subir a Cloudinary
                $upload = (new \Cloudinary\Api\Upload\UploadApi())->upload($image->getRealPath(), [
                    'public_id' => $fileName,
                ]);
                
                $product->images()->create([
                    'image_path' => $fileName,
                    'is_primary' => $existingCount === 0 && $index === 0,
                    'sort_order' => $existingCount + $index,
                ]);
            }
        }

        $this->clearCache();

        return redirect()->route('admin.products.index')
            ->with('success', 'Producto actualizado correctamente');
    }

    public function destroy(Request $request, Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);

        $status = $product->is_active ? 'activado' : 'desactivado';

        $this->clearCache();

        // Mantener los filtros actuales
        return redirect()->route('admin.products.index', $request->only(['search', 'category', 'status']))
            ->with('success', "Producto {$status} correctamente");
    }


    public function uploadImage(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $baseName = Str::slug($product->name);
        $existingCount = $product->images()->count();
        $fileName = $baseName . '-' . ($existingCount + 1);

        // Subir a Cloudinary
        $upload = (new \Cloudinary\Api\Upload\UploadApi())->upload($request->file('image')->getRealPath(), [
            'public_id' => $fileName,
        ]);

        $product->images()->create([
            'image_path' => $fileName,
            'is_primary' => $existingCount === 0,
            'sort_order' => $existingCount,
        ]);

        return back()->with('success', 'Imagen subida correctamente');
    }

    public function deleteImage(ProductImage $image)
    {
        $productId = $image->product_id;
        $wasPrimary = $image->is_primary;
        
        // Eliminar de Cloudinary (opcional)
        try {
            (new \Cloudinary\Api\Admin\AdminApi())->deleteAssets([$image->image_path]);
        } catch (\Exception $e) {
            // Continuar aunque falle
        }
        
        $image->delete();

        // Si era la principal, asignar la siguiente como principal
        if ($wasPrimary) {
            $nextImage = ProductImage::where('product_id', $productId)
                ->orderBy('sort_order')
                ->first();
                
            if ($nextImage) {
                $nextImage->update(['is_primary' => true]);
            }
        }

        return back()->with('success', 'Imagen eliminada');
    }

    private function clearCache()
    {
        Cache::forget('featured_products');
        Cache::forget('latest_products');
        Cache::forget('categories');
        Cache::forget('categories_with_count');
        
        for ($i = 1; $i <= 10; $i++) {
            Cache::forget('catalog_products_page_' . $i);
        }
    }

}