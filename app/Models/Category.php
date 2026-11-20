<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'name_en', 'slug', 'description', 'description_en', 'image', 'is_active', 'sort_order', 'size_options'];

    protected $casts = [
        'size_options' => 'array',
    ];

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

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
