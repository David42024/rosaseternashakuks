<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image_path',
        'is_primary',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // ==========================================
    // RELACIONES
    // ==========================================

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getImageUrlAttribute(): string
    {
        if ($this->image_path) {
            return 'https://res.cloudinary.com/dl4y5pap8/image/upload/' . $this->image_path . '.jpg';
        }
        return asset('images/default-product.png');
    }

    protected $appends = [
        'image_url',
    ];
}