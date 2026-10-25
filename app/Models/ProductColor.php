<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductColor extends Model
{
    protected $fillable = ['name', 'name_en', 'hex', 'is_active', 'sort_order'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /** @return array<string, string> name => hex */
    public static function hexMap(): array
    {
        return static::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('hex', 'name')
            ->all();
    }

    /** @return array<string, string> name => hex for product create/edit checkboxes */
    public static function optionsForForms(?Product $product = null): array
    {
        $options = static::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (self $c) => [$c->name => $c->hex])
            ->all();

        if ($product) {
            $existing = is_array($product->available_colors)
                ? $product->available_colors
                : (json_decode($product->available_colors ?? '[]', true) ?? []);

            foreach ($existing as $col) {
                if (! is_array($col)) {
                    continue;
                }
                $name = $col['name'] ?? '';
                if ($name !== '' && ! isset($options[$name])) {
                    $options[$name] = $col['hex'] ?? '#cccccc';
                }
            }
        }

        return $options;
    }
}
