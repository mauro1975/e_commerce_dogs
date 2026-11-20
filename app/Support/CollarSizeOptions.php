<?php

namespace App\Support;

use App\Models\Category;

class CollarSizeOptions
{
  public static function collarCategory(): ?Category
  {
    return Category::whereIn('slug', ['collari', 'collars'])->first();
  }

  /**
   * @return list<array{size: string, length_min: int|float, length_max: int|float, height: float, sort: int}>
   */
  public static function defaults(): array
  {
    return [
      ['size' => '25', 'length_min' => 17, 'length_max' => 22, 'height' => 1.2, 'sort' => 1],
      ['size' => '30', 'length_min' => 23, 'length_max' => 27, 'height' => 1.2, 'sort' => 2],
      ['size' => '35', 'length_min' => 27, 'length_max' => 32, 'height' => 1.5, 'sort' => 3],
      ['size' => '40', 'length_min' => 28, 'length_max' => 36, 'height' => 2.0, 'sort' => 4],
    ];
  }

  /**
   * @return list<array{size: string, length_min: int|float, length_max: int|float, height: float, sort: int}>
   */
  public static function forCategory(?Category $category): array
  {
    if (! $category || ! in_array($category->slug, ['collari', 'collars'], true)) {
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
        'length_min' => (float) ($row['length_min'] ?? 0),
        'length_max' => (float) ($row['length_max'] ?? 0),
        'height' => (float) ($row['height'] ?? 0),
        'sort' => (int) ($row['sort'] ?? 0),
      ];
    }

    usort($rows, fn ($a, $b) => ($a['sort'] <=> $b['sort']) ?: strcmp($a['size'], $b['size']));

    return $rows !== [] ? $rows : self::defaults();
  }

  public static function formatCmNumber(float $value): string
  {
    $formatted = number_format($value, 1, ',', '');

    return rtrim(rtrim($formatted, '0'), ',');
  }

  /**
   * Es. "25 - da 17 cm a 22 cm, h 1,2 cm"
   */
  public static function label(array $option): string
  {
    $size = $option['size'] ?? '';
    $min = self::formatCmNumber((float) ($option['length_min'] ?? 0));
    $max = self::formatCmNumber((float) ($option['length_max'] ?? 0));
    $h = self::formatCmNumber((float) ($option['height'] ?? 0));

    return "{$size} - da {$min} cm a {$max} cm, h {$h} cm";
  }

  /**
   * @param  list<string>  $selectedSizes
   * @return list<array{size: string, length_min: int|float, length_max: int|float, height: float, sort: int, label: string}>
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