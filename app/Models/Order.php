<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'address',
        'notes',
        'subtotal',
        'total',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    // Estados posibles
    const STATUS_PENDIENTE = 'pendiente';
    const STATUS_CONFIRMADO = 'confirmado';
    const STATUS_PREPARANDO = 'preparando';
    const STATUS_ENTREGADO = 'entregado';
    const STATUS_CANCELADO = 'cancelado';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDIENTE => 'Pendiente',
            self::STATUS_CONFIRMADO => 'Confirmado',
            self::STATUS_PREPARANDO => 'En Preparación',
            self::STATUS_ENTREGADO => 'Entregado',
            self::STATUS_CANCELADO => 'Cancelado',
        ];
    }

    // ==========================================
    // RELACIONES
    // ==========================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePendientes($query)
    {
        return $query->where('status', self::STATUS_PENDIENTE);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDIENTE => 'yellow',
            self::STATUS_CONFIRMADO => 'blue',
            self::STATUS_PREPARANDO => 'purple',
            self::STATUS_ENTREGADO => 'green',
            self::STATUS_CANCELADO => 'red',
            default => 'gray',
        };
    }

    public function getItemsCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}