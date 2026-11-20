<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            ['size' => 'XS', 'sort' => 1],
            ['size' => 'S', 'sort' => 2],
            ['size' => 'M', 'sort' => 3],
            ['size' => 'L', 'sort' => 4],
        ];

        DB::table('categories')
            ->whereIn('slug', ['pettorine', 'harnesses'])
            ->update(['size_options' => json_encode($defaults)]);
    }

    public function down(): void
    {
        // no-op
    }
};