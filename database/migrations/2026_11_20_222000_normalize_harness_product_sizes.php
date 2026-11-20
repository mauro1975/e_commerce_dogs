<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $categoryIds = DB::table('categories')
            ->whereIn('slug', ['pettorine', 'harnesses'])
            ->pluck('id');

        if ($categoryIds->isEmpty()) {
            return;
        }

        $sizes = json_encode(['XS', 'S', 'M', 'L']);

        DB::table('products')
            ->whereIn('category_id', $categoryIds)
            ->update(['available_sizes' => $sizes]);
    }

    public function down(): void
    {
        // no-op
    }
};