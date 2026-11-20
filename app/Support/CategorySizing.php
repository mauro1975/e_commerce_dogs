<?php

namespace App\Support;

use App\Models\Category;

class CategorySizing
{
    /**
     * Categorie senza varianti di taglia (es. porta sacchetti).
     *
     * @return list<string>
     */
    public static function slugsWithoutSizes(): array
    {
        return ['bag-holders', 'porta-sacchetti'];
    }

    public static function categoryUsesSizes(?Category $category): bool
    {
        if (! $category) {
            return true;
        }

        return ! in_array($category->slug, self::slugsWithoutSizes(), true);
    }

    public static function productUsesSizes(?Category $category, mixed $availableSizes): bool
    {
        if (! self::categoryUsesSizes($category)) {
            return false;
        }

        if (is_string($availableSizes)) {
            $availableSizes = json_decode($availableSizes, true) ?? [];
        }

        return is_array($availableSizes) && $availableSizes !== [];
    }

    public static function bagHolderCategory(): ?Category
    {
        return Category::whereIn('slug', self::slugsWithoutSizes())->orderBy('id')->first();
    }

    /**
     * @return list<int>
     */
    public static function noSizeCategoryIds(): array
    {
        return Category::whereIn('slug', self::slugsWithoutSizes())
            ->orderBy('id')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }
}