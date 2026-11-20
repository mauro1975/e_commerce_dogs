<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $categoryIds = DB::table('categories')
            ->where('slug', 'bag-holders')
            ->pluck('id');

        if ($categoryIds->isEmpty()) {
            return;
        }

        $productIds = DB::table('products')
            ->whereIn('category_id', $categoryIds)
            ->pluck('id');

        if ($productIds->isEmpty()) {
            return;
        }

        DB::table('products')
            ->whereIn('id', $productIds)
            ->update(['available_sizes' => json_encode([])]);

        DB::table('product_variants')
            ->whereIn('product_id', $productIds)
            ->where('size', '!=', '')
            ->delete();
    }

    public function down(): void
    {
        // no-op
    }
};