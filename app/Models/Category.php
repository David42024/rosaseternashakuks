<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ==========================================
    // RELACIONES
    // ==========================================

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ==========================================
    // MUTATORS / ACCESSORS
    // ==========================================

    protected static function boot()
    {
        parent::boot();

        // Auto-generar slug al crear
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // Accessor para imagen con fallback
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return 'https://res.cloudinary.com/dl4y5pap8/image/upload/' . $this->image . '.jpg';
        }

        return asset('images/default-category.png');
    }

    protected $appends = [
        'image_url',
    ];
}