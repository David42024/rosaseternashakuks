<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        // Redirigir a status=active si no hay filtro
        if (!$request->has('status')) {
            return redirect()->route('admin.categories.index', ['status' => 'active']);
        }

        $query = Category::withCount('products');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $categories = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Categories/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Subir imagen a Cloudinary
        if ($request->hasFile('image')) {
            $fileName = 'cat-' . Str::slug($validated['name']);
            
            $upload = (new \Cloudinary\Api\Upload\UploadApi())->upload($request->file('image')->getRealPath(), [
                'public_id' => $fileName,
            ]);
            
            $validated['image'] = $fileName;
        }

        Category::create($validated);

        $this->clearCache();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoría creada correctamente');
    }

    public function edit(Category $category)
    {
        return Inertia::render('Admin/Categories/Edit', [
            'category' => $category,
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Subir nueva imagen a Cloudinary
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior de Cloudinary
            if ($category->image) {
                try {
                    (new \Cloudinary\Api\Admin\AdminApi())->deleteAssets([$category->image]);
                } catch (\Exception $e) {
                    // Continuar aunque falle
                }
            }
            
            $fileName = 'cat-' . Str::slug($validated['name']);
            
            $upload = (new \Cloudinary\Api\Upload\UploadApi())->upload($request->file('image')->getRealPath(), [
                'public_id' => $fileName,
            ]);
            
            $validated['image'] = $fileName;
        }

        $category->update($validated);

        $this->clearCache();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoría actualizada correctamente');
    }

    public function destroy(Request $request, Category $category)
    {
        // Toggle activo/inactivo en lugar de eliminar
        $category->update(['is_active' => !$category->is_active]);

        $status = $category->is_active ? 'activada' : 'desactivada';

        $this->clearCache();

        return redirect()->route('admin.categories.index', $request->only(['search', 'status']))
            ->with('success', "Categoría {$status} correctamente");
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