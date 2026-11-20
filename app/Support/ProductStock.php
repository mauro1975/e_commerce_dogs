<?php

namespace App\Support;

use App\Models\Product;
use App\Models\ProductVariant;

class ProductStock
{
    /** Giacenza sul prodotto: varianti esistono ma nessuna ha stock > 0 */
    public static function usesProductPool(Product $product): bool
    {
        if ((int) $product->stock <= 0) {
            return false;
        }

        return ! $product->variants()->where('stock', '>', 0)->exists();
    }

    public static function availableFor(Product $product, ?string $color, ?string $size): int
    {
        $variant = null;
        if ($color !== null && $color !== '' && $size !== null && $size !== '') {
            $variant = $product->relationLoaded('variants')
                ? $product->variants->first(fn (ProductVariant $v) => $v->color === $color && (string) $v->size === (string) $size)
                : $product->variants()->where('color', $color)->where('size', $size)->first();
        }

        if ($variant && (int) $variant->stock > 0) {
            return (int) $variant->stock;
        }

        if (self::usesProductPool($product)) {
            return (int) $product->stock;
        }

        if ($variant) {
            return (int) $variant->stock;
        }

        return self::usesProductPool($product)
            ? (int) $product->stock
            : 0;
    }

    public static function decrementAfterOrder(Product $product, ?ProductVariant $variant, int $quantity): void
    {
        if ($variant && (int) $variant->stock > 0) {
            $variant->decrement('stock', $quantity);

            return;
        }

        $product->decrement('stock', $quantity);
    }
}