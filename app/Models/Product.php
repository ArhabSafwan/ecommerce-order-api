<?php

namespace App\Models;

use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'vendor_id',
        'name',
        'slug',
        'sku',
        'description',
        'price',
        'is_active',
        'low_stock_threshold'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'low_stock_threshold' => 'integer',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    // total inventory across variants
    public function getTotalQuantityAttribute()
    {
        return $this->variants->sum(fn($v) => $v->inventory?->quantity ?? 0);
    }
}
