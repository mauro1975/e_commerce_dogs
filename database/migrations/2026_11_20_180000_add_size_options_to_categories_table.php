<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->json('size_options')->nullable()->after('description_en');
        });

        $defaults = [
            ['size' => '25', 'length_min' => 17, 'length_max' => 22, 'height' => 1.2, 'sort' => 1],
            ['size' => '30', 'length_min' => 23, 'length_max' => 27, 'height' => 1.2, 'sort' => 2],
            ['size' => '35', 'length_min' => 27, 'length_max' => 32, 'height' => 1.5, 'sort' => 3],
            ['size' => '40', 'length_min' => 28, 'length_max' => 36, 'height' => 2.0, 'sort' => 4],
        ];

        DB::table('categories')
            ->whereIn('slug', ['collari', 'collars'])
            ->update(['size_options' => json_encode($defaults)]);
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('size_options');
        });
    }
};