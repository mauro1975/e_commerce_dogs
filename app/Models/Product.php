<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'name_en', 'slug', 'sku', 'description', 'description_en', 'price', 'compare_price',
        'discount_percent', 'stock', 'image', 'images', 'available_colors', 'available_sizes', 'size_chart',
        'is_active', 'is_featured', 'is_best_seller', 'seo_title', 'seo_description',
    ];

    protected $casts = [
        'images' => 'array',
        'available_colors' => 'array',
        'available_sizes' => 'array',
        'size_chart' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_best_seller' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function getNameAttribute(string $value): string
    {
        if (app()->getLocale() === 'en' && !empty($this->attributes['name_en'])) {
            return $this->attributes['name_en'];
        }
        return $value;
    }

    public function getDescriptionAttribute(?string $value): ?string
    {
        if (app()->getLocale() === 'en' && !empty($this->attributes['description_en'])) {
            return $this->attributes['description_en'];
        }
        return $value;
    }

    public function getFinalPriceAttribute(): float
    {
        if ($this->discount_percent > 0) {
            return round($this->price * (1 - $this->discount_percent / 100), 2);
        }
        return (float) $this->price;
    }

    public function isLeatherProduct(): bool
    {
        $parts = array_filter([
            $this->attributes['name'] ?? '',
            $this->attributes['name_en'] ?? '',
            $this->attributes['description'] ?? '',
            $this->attributes['description_en'] ?? '',
        ]);
        $text = strtolower(implode(' ', $parts));

        return str_contains($text, 'pelle') || str_contains($text, 'leather');
    }
}
