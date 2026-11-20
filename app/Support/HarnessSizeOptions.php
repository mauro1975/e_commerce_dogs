<?php

namespace App\Support;

use App\Models\Category;

class HarnessSizeOptions
{
    public static function harnessCategory(): ?Category
    {
        return Category::whereIn('slug', ['pettorine', 'harnesses'])->first();
    }

    /**
     * @return list<array{size: string, sort: int}>
     */
    public static function defaults(): array
    {
        return [
            ['size' => 'XS', 'sort' => 1],
            ['size' => 'S', 'sort' => 2],
            ['size' => 'M', 'sort' => 3],
            ['size' => 'L', 'sort' => 4],
        ];
    }

    /**
     * @return list<array{size: string, sort: int}>
     */
    public static function forCategory(?Category $category): array
    {
        if (! $category || ! in_array($category->slug, ['pettorine', 'harnesses'], true)) {
            return [];
        }

        $raw = $category->size_options;
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }
        if (! is_array($raw) || $raw === []) {
            return self::defaults();
        }

        $rows = [];
        foreach ($raw as $row) {
            if (! is_array($row) || empty($row['size'])) {
                continue;
            }
            $rows[] = [
                'size' => (string) $row['size'],
                'sort' => (int) ($row['sort'] ?? 0),
            ];
        }

        usort($rows, fn ($a, $b) => ($a['sort'] <=> $b['sort']) ?: strcmp($a['size'], $b['size']));

        return $rows !== [] ? $rows : self::defaults();
    }

    public static function label(array $option): string
    {
        return (string) ($option['size'] ?? '');
    }

    /**
     * @param  list<string>  $selectedSizes
     * @return list<array{size: string, sort: int, label: string}>
     */
    public static function forProductSizes(?Category $category, array $selectedSizes): array
    {
        $catalog = self::forCategory($category);
        if ($catalog === []) {
            return [];
        }

        $selected = array_map('strval', $selectedSizes);
        $out = [];
        foreach ($catalog as $row) {
            if (! in_array($row['size'], $selected, true)) {
                continue;
            }
            $row['label'] = self::label($row);
            $out[] = $row;
        }

        return $out;
    }
}