<?php

namespace App\Support;

use App\Models\Product;

class PdpSizeGuide
{
    /**
     * @param  array<int, string>|null  $sizes
     * @return array{
     *     rows: list<array<string, mixed>>,
     *     guideRows: list<array<string, mixed>>,
     *     active: bool,
     *     maxChest: float
     * }
     */
    public static function build(Product $product, ?array $sizes = null): array
    {
        $guideCatalog = trans('front.pdp_dog_size_guide');
        if (! is_array($guideCatalog)) {
            $guideCatalog = [];
        }

        $defaults = trans('front.pdp_sweater_size_defaults');
        if (! is_array($defaults)) {
            $defaults = [];
        }

        $custom = $product->size_chart ?? null;
        if (is_string($custom)) {
            $custom = json_decode($custom, true);
        }

        $customRows = [];
        if (is_array($custom) && ! empty($custom['rows']) && is_array($custom['rows'])) {
            foreach ($custom['rows'] as $row) {
                if (! empty($row['size'])) {
                    $customRows[$row['size']] = $row;
                }
            }
        }

        $sizeOrder = ['XXS', 'XS', 'S', 'M', 'L'];

        $guideOrder = array_values(array_filter(
            $sizeOrder,
            fn (string $sz) => isset($guideCatalog[$sz]) || isset($defaults[$sz]) || isset($customRows[$sz])
        ));

        $productOrder = ! empty($sizes)
            ? array_values(array_map(fn ($s) => is_string($s) ? $s : (string) $s, $sizes))
            : $guideOrder;

        $guideRows = [];
        foreach ($guideOrder as $sz) {
            $merged = self::mergedRow($sz, $guideCatalog, $defaults, $customRows);
            if ($merged !== null) {
                $guideRows[] = $merged;
            }
        }

        $rows = [];
        foreach ($productOrder as $sz) {
            $merged = self::mergedRow($sz, $guideCatalog, $defaults, $customRows);
            if ($merged === null) {
                continue;
            }

            $chestDisplay = $merged['chest'] ?? ($merged['chest_num'] ?? null);
            $chestNum = $merged['chest_num'] ?? null;
            if ($chestNum === null && is_numeric($chestDisplay)) {
                $chestNum = (float) $chestDisplay;
            }

            $rows[] = [
                'size'      => $sz,
                'size_note' => $merged['size_note'] ?? null,
                'back'      => $merged['back'] ?? null,
                'neck'      => $merged['neck'] ?? null,
                'chest'     => $chestDisplay,
                'chest_num' => $chestNum,
            ];
        }

        $maxChest = max(1, ...array_map(
            fn ($r) => (float) ($r['chest_num'] ?? 0),
            $rows ?: [['chest_num' => 1]]
        ));

        return [
            'rows'      => $rows,
            'guideRows' => $guideRows,
            'active'    => count($guideRows) > 0,
            'maxChest'  => $maxChest,
        ];
    }

    /**
     * @param  array<string, mixed>  $guideCatalog
     * @param  array<string, mixed>  $defaults
     * @param  array<string, mixed>  $customRows
     * @return array<string, mixed>|null
     */
    private static function mergedRow(string $sz, array $guideCatalog, array $defaults, array $customRows): ?array
    {
        $base = $guideCatalog[$sz] ?? $defaults[$sz] ?? [];
        $over = $customRows[$sz] ?? [];

        if ($base === [] && $over === []) {
            return null;
        }

        $merged = array_merge($base, $over);
        $merged['size'] = $sz;

        return $merged;
    }
}