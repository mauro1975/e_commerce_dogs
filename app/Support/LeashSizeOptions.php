<?php

namespace App\Support;

use App\Models\Category;

class LeashSizeOptions
{
    public static function leashCategory(): ?Category
    {
        return Category::whereIn('slug', ['guinzagli', 'leashes'])->first();
    }

    /**
     * @return list<array{size: string, width_mm: float, length_m: float, sort: int}>
     */
    public static function defaults(): array
    {
        return [
            ['size' => '12', 'width_mm' => 12, 'length_m' => 1.2, 'sort' => 1],
            ['size' => '15', 'width_mm' => 15, 'length_m' => 1.2, 'sort' => 2],
        ];
    }

    /**
     * @return list<array{size: string, width_mm: float, length_m: float, sort: int}>
     */
    public static function forCategory(?Category $category): array
    {
        if (! $category || ! in_array($category->slug, ['guinzagli', 'leashes'], true)) {
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
                'width_mm' => (float) ($row['width_mm'] ?? $row['size']),
                'length_m' => (float) ($row['length_m'] ?? 1.2),
                'sort' => (int) ($row['sort'] ?? 0),
            ];
        }

        usort($rows, fn ($a, $b) => ($a['sort'] <=> $b['sort']) ?: strcmp($a['size'], $b['size']));

        return $rows !== [] ? $rows : self::defaults();
    }

    public static function formatDecimal(float $value, int $decimals = 2): string
    {
        $formatted = number_format($value, $decimals, ',', '');

        return rtrim(rtrim($formatted, '0'), ',');
    }

    /**
     * Es. "12 mm – lunghezza 1,20 m"
     */
    public static function label(array $option): string
    {
        $width = self::formatDecimal((float) ($option['width_mm'] ?? $option['size'] ?? 0), 0);
        $length = self::formatDecimal((float) ($option['length_m'] ?? 1.2), 2);

        return "{$width} mm – lunghezza {$length} m";
    }

    /**
     * @param  list<string>  $selectedSizes
     * @return list<array{size: string, width_mm: float, length_m: float, sort: int, label: string}>
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