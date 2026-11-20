<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            ['size' => '12', 'width_mm' => 12, 'length_m' => 1.2, 'sort' => 1],
            ['size' => '15', 'width_mm' => 15, 'length_m' => 1.2, 'sort' => 2],
        ];

        DB::table('categories')
            ->whereIn('slug', ['guinzagli', 'leashes'])
            ->update(['size_options' => json_encode($defaults)]);
    }

    public function down(): void
    {
        // no-op
    }
};